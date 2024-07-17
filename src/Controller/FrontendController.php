<?php

namespace App\Controller;

use RuntimeException;
use Symfony\Component\HttpFoundation\{
    Request,
    Response,
};
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use App\Entity\Pages\{
    Page,
    Home as HomePage,
    Event as EventPage,
    Contact as ContactPage,
    Issue as IssuePage,
    IssueNotFoundException,
};

use function Stringy\create as s;
use function Functional\last;

class FrontendController extends AbstractController
{
    /**
     * @throws RuntimeException The page template was not found.
     */
    protected function loadPage(string $path, array $data): Response
    {
        $templatesDir = __DIR__ . '/../../templates';

        $tplPaths = [
            "routes/$path",
            "page",
        ];

        foreach ($tplPaths as $tplPath) {
            $tpl = "$tplPath.html.twig";
            if (is_readable("$templatesDir/$tpl")) {
                $data['routeName'] = $path;
                $data['tpl'] = str_replace(['routes/', '/'], ['', '-'], $tplPath);
                return $this->render($tpl, $data);
            }
        }

        throw new RuntimeException("Could not find a template.");
    }

    protected function page(string $path, $dataOrPage): Response
    {
        $data = is_array($dataOrPage) ? $dataOrPage : $dataOrPage->getPageData();
        return $this->loadPage($path, $data);
    }

    /**
     * @Route("/{slug}", name="page")
     */
    public function defaultPage(string $slug, Page $page): Response
    {
        $pageName = (string) s($slug)->camelize();

        if ($pageName === 'new') {
            $pageName = 'pageNew';
        }

        return $this->page($pageName, $page);
    }

    /**
     * @Route("/", name="home", priority=2)
     */
    public function home(HomePage $page): Response
    {
        return $this->page(__FUNCTION__, $page);
    }

    /**
     * @Route("/contact", name="contact", priority=2)
     */
    public function contact(ContactPage $page, Request $request, MailerInterface $mailer): Response
    {
        $data = $page->getPageData();
        $data['form'] = $page->createContactForm($this->createFormBuilder());
        if ($page->canProcessForm($data['form'], $request)) {
            try {
                $page->mailMessage($mailer, $data['form']->getData());
                $data['message_success'] = true;
            } catch (TransportExceptionInterface $e) {
                $data['form']->get('email')
                             ->addError(new FormError('Could not send email.'));
            }
        }
        $data['form_view'] = $data['form']->createView();
        return $this->page(__FUNCTION__, $data);
    }

    /**
     * @Route("/event", name="event", priority=2)
     */
    public function event(EventPage $page): Response
    {
        return $this->page(__FUNCTION__, $page);
    }

    /**
     * @Route("/issues", name="issues", priority=2)
     */
    public function issues(): Response
    {
        return $this->page(__FUNCTION__, []);
    }

    /**
     * @Route("/issues/{id<\d+>}-{slug}/layouts", name="issue-layouts", priority=2)
     */
    public function issueLayouts(int $id, string $slug, IssuePage $issuePage): Response
    {
        $path = (string) s(__FUNCTION__)->snakeize()->replace('_', '/');
        try {
            $issuePage->fetchIssue($id, $slug);
        } catch (IssueNotFoundException $e) {
            throw $this->createNotFoundException('Could not find the specified issue.');
        }
        return $this->page($path, $issuePage);
    }

    /**
     * @Route("/models/{slug}", name="model", priority=2)
     */
    public function model(string $slug, Page $page): Response
    {
        return $this->page(__FUNCTION__, $page);
    }

    public function error(\Throwable $exception, Page $page): Response
    {
        $statusCode = $exception instanceof HttpException ? $exception->getStatusCode() : 500;
        $data = $page->getPageData("/$statusCode");
        $errorType = last(explode('\\', get_class($exception)));

        if ($data['page'] == null) {
            $data['page'] = ['title' => $errorType, 'content' => ''];
        }
        if ($data['page']['title'] == 'Error') {
            $data['page']['title'] = $errorType;
        }

        if (empty($data['page']['content'])) {
            $data['page']['content'] = $exception->getMessage();
        }

        $response = $this->page(__FUNCTION__, $data);
        $response->setStatusCode($statusCode);
        return $response;
    }
}

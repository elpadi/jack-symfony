<?php

namespace App\Controller;

use RuntimeException;
use ReflectionClass;
use Throwable;
use Symfony\Component\HttpFoundation\{
    Request,
    Response,
    RedirectResponse
};
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Twig\{
    TwigFunction,
    TwigFilter
};
use cebe\markdown\Markdown;
use App\Entity\Pages\{
    Page,
    Home as HomePage,
    Event as EventPage,
    Contact as ContactPage,
    Issue as IssuePage
};
use App\Exception\ItemNotFoundException;
use App\Helpers\Twig as TwigHelper;

use function Stringy\create as s;
use function Functional\last;

class FrontendController extends AbstractController
{
    protected function enableMarkdownTemplates(): void
    {
        $twig = $this->get('twig');
        $twig->addFunction(new TwigFunction('region', function ($name) {
            return (new Markdown())->parse(file_get_contents(static::getTemplateDir() . "/regions/$name.md"));
        }));
        $twig->addFilter(new TwigFilter('md', function ($text) {
            return (new Markdown())->parse($text);
        }));
        $twig->addFilter(new TwigFilter('mdp', function ($text) {
            return (new Markdown())->parseParagraph($text);
        }));
    }

    /**
     * @throws RuntimeException The page template was not found.
     */
    protected function loadPage(string $path, array $data): Response
    {
        $twigLoader = $this->get('twig')->getLoader();
        TwigHelper::init($this->get('twig'));

        $tplPaths = [
            "routes/$path",
            "page",
        ];

        foreach ($tplPaths as $tplPath) {
            $tpl = "$tplPath.html.twig";
            if ($twigLoader->exists($tpl)) {
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
        $this->enableMarkdownTemplates();
        return $this->loadPage($path, $data);
    }

    /**
     * @Route("/", name="home")
     */
    public function home(HomePage $page): Response
    {
        return $this->page(__FUNCTION__, $page);
    }

    /**
     * @Route("/jbpc", name="jbpc")
     */
    public function jbpc(): Response
    {
        return $this->page(__FUNCTION__, [
            'meta' => [
                'title' => 'Jack Black Pussy Cat',
                'description' => 'New issue',
            ],
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about(Page $page): Response
    {
        return $this->page(__FUNCTION__, $page);
    }

    /**
     * @Route("/contact", name="contact")
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
     * @Route("/event", name="event")
     */
    public function event(EventPage $page): Response
    {
        return $this->page(__FUNCTION__, $page);
    }

    /**
     * @Route("/issues", name="issues")
     */
    public function issues(): Response
    {
        return $this->page(__FUNCTION__, []);
    }

    /**
     * @Route("/issues/{id<\d+>}-{slug}/layouts", name="issue-layouts")
     */
    public function issueLayouts(int $id, string $slug, IssuePage $issuePage): Response
    {
        $path = (string) s(__FUNCTION__)->snakeize()->replace('_', '/');
        try {
            $issuePage->fetchIssue($id, $slug);
        } catch (ItemNotFoundException $e) {
            throw $this->createNotFoundException('Could not find the specified issue.');
        }
        return $this->page($path, $issuePage);
    }

    /**
     * @Route("/special-project")
     */
    public function specialProject(Page $page): Response
    {
        return $this->page(__FUNCTION__, $page);
    }

    /**
     * @Route("/new", name="page-new")
     */
    public function pageNew(Page $page): Response
    {
        return $this->page(__FUNCTION__, $page);
    }

    /**
     * @Route("/making-of-jack")
     */
    public function makingOfJack(Page $page): Response
    {
        return $this->page(__FUNCTION__, $page);
    }

    /**
     * @Route("/galore")
     */
    public function galore(Page $page): Response
    {
        return $this->page(__FUNCTION__, $page);
    }

    /**
     * @Route("/stop-asian-hate")
     */
    public function stopAsianHate(Page $page): Response
    {
        return $this->page(__FUNCTION__, $page);
    }

    /**
     * @Route("/models")
     */
    public function models(Page $page): Response
    {
        return $this->page(__FUNCTION__, $page);
    }

    /**
     * @Route("/models/{slug}", name="model")
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

<?php
namespace App\Controller;

use RuntimeException;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\{
    FormRenderer,
    Forms
};
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use App\Entity\Pages\{
    Page,
    Home as HomePage,
    Event as EventPage,
    Contact as ContactPage
};

class FrontendController extends AbstractController
{
    protected function page(string $name, $dataOrPage): Response
    {
        $data = is_array($dataOrPage) ? $dataOrPage : $dataOrPage->getPageData();
        $twigLoader = $this->get('twig')->getLoader();

        foreach ([
            "routes/$name",
            "page",
        ] as $route) {
            $path = "$route.html.twig";
            if ($twigLoader->exists($path)) {
                return $this->render($path, $data);
            }
        }

        throw new RuntimeException("Could not find a template.");
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
    public function contact(ContactPage $page): Response
    {
        return $this->page(__FUNCTION__, $page);
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
        return $this->page(__FUNCTION__);
    }

    /**
     * @Route("/issues/{id<\d+>}-{slug}/layouts", name="issue-layouts")
     */
    public function issueLayouts(): Response
    {
        return $this->page(__FUNCTION__);
    }

    /**
     * @Route("/special-project")
     */
    public function special_project(): Response
    {
        return $this->page(__FUNCTION__);
    }

    /**
     * @Route("/new", name="new")
     */
    public function page_new(): Response
    {
        return $this->page(__FUNCTION__);
    }
}

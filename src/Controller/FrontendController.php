<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Pages\{
    Page,
    Home
};
use Twig\Error\LoaderError;

class FrontendController extends AbstractController
{
    protected function page(string $name, $dataOrPage): Response
    {
        $data = is_array($dataOrPage) ? $dataOrPage : $dataOrPage->getPageData();
        try {
            return $this->render("routes/$name.html.twig", $data);
        }
        catch (LoaderError $e) {
            if (substr_count($e->getMessage(), 'find')) {
                return $this->render("page.html.twig", $data);
            }
            throw $e;
        }
        return 'Unspecified error';
    }

    /**
     * @Route("/", name="home")
     */
    public function home(Home $home): Response
    {
        return $this->page(__FUNCTION__, $home);
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
    public function contact(Page $page): Response
    {
        return $this->page(__FUNCTION__, $page);
    }

    /**
     * @Route("/event", name="event")
     */
    public function event(Page $page): Response
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

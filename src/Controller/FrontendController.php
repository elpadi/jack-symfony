<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Magazine\Intro;
use App\Entity\Pages\JackBlackPussyCat;

class FrontendController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */    
    public function home(JackBlackPussyCat $jbpc): Response
    {
        $data = $jbpc->getPageData();
        if (!isset($_COOKIE['has_seen_intro']) || $_COOKIE['has_seen_intro'] != '1') {
            $data['intro'] = [
                'images' => (new Intro())->fetchImages(),
                'endRoute' => 'jbpc',
            ];
        }
        return $this->page('jbpc', $data);
    }

    protected function page(string $name, array $data = []): Response
    {
        return $this->render("routes/$name.html.twig", $data);
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
    public function about(): Response
    {
        return $this->page(__FUNCTION__);
    }

    /**
     * @Route("/contact", name="contact")
     */    
    public function contact(): Response
    {
        return $this->page(__FUNCTION__);
    }

    /**
     * @Route("/event", name="event")
     */    
    public function event(): Response
    {
        return $this->page(__FUNCTION__);
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

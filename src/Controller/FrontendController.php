<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FrontendController extends AbstractController
{

	/**
	 * @Route("/", name="home")
	 */    
	public function home(): Response
	{
		return $this->jbpc();
	}

	protected function page(string $name, array $data = []): Response
    {
		return $this->render("routes/$name.html.twig", array_merge([], $data));
    }

	/**
	 * @Route("/jbpc")
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
	 * @Route("/about")
	 */    
	public function about(): Response
    {
		return $this->page(__FUNCTION__);
    }

	/**
	 * @Route("/contact")
	 */    
	public function contact(): Response
    {
		return $this->page(__FUNCTION__);
    }

	/**
	 * @Route("/event")
	 */    
	public function event(): Response
    {
		return $this->page(__FUNCTION__);
    }

	/**
	 * @Route("/issues")
	 */    
	public function issues(): Response
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
	 * @Route("/new")
	 */    
	public function page_new(): Response
    {
		return $this->page(__FUNCTION__);
    }

}

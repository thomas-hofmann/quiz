<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController {
	#[Route('/')]
	public function indexAction(): Response {
		return $this->render('index/index.html.twig', [
		]);
	}

	#[Route('/imprint', name: 'imprint')]
	public function imprintAction(): Response {
		return $this->render('index/imprint.html.twig', [
		]);
	}

	#[Route('/privacy', name: 'privacy')]
	public function privacyAction(): Response {
		return $this->render('index/privacy.html.twig', [
		]);
	}
}
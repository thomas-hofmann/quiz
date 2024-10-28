<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController {
	#[Route('/', name: 'home')]
	public function indexAction(Request $request): Response {
		$session = $request->getSession();
		$session->remove('code');

		if ($request->get('quizcode')) {
			return $this->redirectToRoute('quiz-initial', ['code' => $request->get('quizcode')]);
		}
		return $this->render('index/index.html.twig', [
		]);
	}

	#[Route('/register', name: 'register')]
	public function registerAction(): Response {
		return $this->render('index/register.html.twig', [
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
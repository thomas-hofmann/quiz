<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Twig\Environment;

class ErrorController extends AbstractController
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function show(FlattenException $exception): Response
    {
        $statusCode = $exception->getStatusCode();

        return new Response(
            $this->twig->render(
                sprintf('bundles/TwigBundle/Exception/error%d.html.twig', $statusCode),
                ['exception' => $exception]
            ),
            $statusCode
        );
    }
}

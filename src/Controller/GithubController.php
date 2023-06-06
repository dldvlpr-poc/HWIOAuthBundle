<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GithubController extends AbstractController
{
    #[Route('/github', name: 'app_github')]
    public function index(): Response
    {
        return $this->render('github/index.html.twig', [
            'controller_name' => 'GithubController',
        ]);
    }
}

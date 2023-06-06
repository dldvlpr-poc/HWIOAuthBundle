<?php

// src/Controller/GitHubController.php
namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GithubController extends AbstractController
{
    /**
     * @Route("/connect/github", name="connect_github_start")
     */
    public function connectAction(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient('github')
            ->redirect();
    }

    /**
     * @Route("/connect/github/check", name="connect_github_check")
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        // cette méthode sera appelée après que l'utilisateur a autorisé l'application
        // vous pouvez obtenir l'utilisateur et faire ce que vous voulez avec lui ici
        $user = $clientRegistry->getClient('github')->fetchUser();

        // ...
    }
}


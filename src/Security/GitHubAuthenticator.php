<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Core\User\UserInterface;

class GitHubAuthenticator implements AuthenticatorInterface
{
    private ClientRegistry $clientRegistry;
    private EntityManagerInterface $em;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
    }

    public function supports(Request $request)
    {
        return $request->getPathInfo() == '/connect/github/check' && $request->isMethod('GET');
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getGitHubClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $githubUser = $this->getGitHubClient()
            ->fetchUserFromToken($credentials);

        $email = $githubUser->getEmail();

        // 1) have they logged in with GitHub before? Easy!
        $existingUser = $this->em->getRepository(User::class)
            ->findOneBy(['githubId' => $githubUser->getId()]);

        if ($existingUser) {
            return $existingUser;
        }

        // 2) do we have a matching user by email?
        $user = $this->em->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        // 3) Maybe you just want to "register" them by creating a User object
        $user->setGithubId($githubUser->getId());
        $user->setGithubAccessToken($credentials->getToken());
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    private function getGitHubClient()
    {
        return $this->clientRegistry
            ->getClient('github');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse('/your/redirect/route');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // todo
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('app_login');
    }

    public function authenticate(Request $request): Passport
    {
        // TODO: Implement authenticate() method.
    }

    public function createToken(Passport $passport, string $firewallName): TokenInterface
    {
        // TODO: Implement createToken() method.
    }
}


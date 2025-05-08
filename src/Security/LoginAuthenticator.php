<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Entity\User;

class LoginAuthenticator extends AbstractAuthenticator

{
    use TargetPathTrait;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'app_login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
    $username = $request->request->get('_username');
    $password = $request->request->get('_password');

    // Ensure the username is not null or empty
    if (empty($username)) {
        throw new \InvalidArgumentException('The username cannot be null or empty.');
    }

    $request->getSession()->set(
        'last_username',
        $username
    );

    return new Passport(
        new UserBadge($username),
        new PasswordCredentials($password),
        [
            new CsrfTokenBadge('login', $request->request->get('_csrf_token')),
            new RememberMeBadge(),
        ]
    );
}
public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
{
    // Récupérer l'utilisateur connecté
    $user = $token->getUser(); // récupère l'utilisateur authentifié

    if ($user instanceof User && $user->getSpecialite() === 'sportif') {
        return new RedirectResponse($this->urlGenerator->generate('app_produituser_index'));
    }
    
    return new RedirectResponse($this->urlGenerator->generate('app_categorie_index'));
    
 
}


    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->set(
            Security::AUTHENTICATION_ERROR,
            $exception
        );

        return null;
    }

    public function supportsRememberMe(): bool
    {
        return true;
    }
}
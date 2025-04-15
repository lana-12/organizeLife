<?php
 
 namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class GoogleAuthenticator extends OAuth2Authenticator
{
    private ClientRegistry $clientRegistry;
    private EntityManagerInterface $em;
    private RouterInterface $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
    }
    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        // Redirection user 
        $client = $this->clientRegistry->getClient('google');
        return new RedirectResponse('/');
    }


    public function supports(Request $request): ?bool
    {
        // Process Authenticator only on this route
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');

        /** @var GoogleUser $googleUser */
        $googleUser = $client->fetchUser();
        $email = $googleUser->getEmail();

        return new SelfValidatingPassport(
            new UserBadge($email, function($userIdentifier) use ($googleUser) {
                $user = $this->em->getRepository(User::class)->findOneBy(['email' => $userIdentifier]);

                if (!$user) {
                    $user = new User();
                    $user->setEmail($googleUser->getEmail());
                    $user->setFirstname($googleUser->getFirstName() ?? '');
                    $user->setLastname($googleUser->getLastName() ?? '');
                    $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
                    $user->setPassword('');
                    $this->em->persist($user);
                    $this->em->flush();
                }
                return $user;
            })
        );
    }


    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('home')); 
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('app_login'));
    }
}

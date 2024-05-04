<?php
namespace App\Security;

use App\Entity\Usuarios;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class CustomAuthenticator extends AbstractAuthenticator {
 
    private $em;
    public function __construct(EntityManagerInterface $em) {
        $this->em=$em; 
    }
    
    public function supports(Request $request): ?bool {
        return $request->headers->has('X-AUTH-TOKEN');
    }

    public function authenticate(Request $request): Passport {

       $apiToken = $request->headers->get('X-AUTH-TOKEN');
       if(null===$apiToken){
            throw new CustomUserMessageAuthenticationException('Se necesita Token de autenticar');
        } 
        try {
            $decode = JWT::decode($request->headers->has('X-AUTH-TOKEN'), new Key($_ENV['JWT_SECRET'], 'HS512'));
            $user = $this->em->getRepository(Usuarios::class)->findOneBy(['id'=>$decode->aud]);
            if(!$user){
                return new Passport( new UserBadge(''), new PasswordCredentials(''));
            } else {
                return new SelfValidatingPassport(new UserBadge($user->getCorreo()));
            }
        } catch (\Throwable $th) {
            return new Passport( new UserBadge(''), new PasswordCredentials(''));
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}


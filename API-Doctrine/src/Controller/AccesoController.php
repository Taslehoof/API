<?php

namespace App\Controller;

use App\Entity\Usuarios;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

//para generar el token
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AccesoController extends AbstractController {

    private $em;
    
    public function __construct(EntityManagerInterface $em){
        $this->em=$em;
    }
    
    #[Route('/api/v1/acceso/login', methods:['POST'])]
    public function login(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse {
    
        $data= json_decode($request->getContent(), true);
        if(!isset($data['correo'])){
            return $this->json([
                'estado'=>'error',
                'mensaje'=>'El campo correo es obligatorio'
            ],200);
        }
        if(!isset($data['password'])){
            return $this->json([
                'estado'=>'error',
                'mensaje'=>'El campo password es obligatorio'
            ],200);
        $user = $this->em->getRepository(Usuarios::class)->findOneBy(['correo'=>$data['correo']]);
        if(!$user){
            return $this->json([
                'estado'=>'error',
                'mensaje'=>"Las credenciales ingresadas no son válidas"
            ], Response::HTTP_BAD_REQUEST);//400
        }

        if ($passwordHasher->isPasswordValid($user, $data['password'])){
            
            $payload = [
                'iss'=>"http://".dirname($_SERVER['SERVER_NAME']."".$_SERVER['PHP_SELF'])."/",
                'aud'=>$user->getId(),
                'iat'=>time(),
                'exp'=>time() + (30*24*60*60)
            ];
            $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS512');
            return $this->json([
                'nombre'=>$user->getNombre(),
                'token'=>$jwt
            ]);
        } else {
            return $this->json([
                'estado'=>'error',
                'mensaje'=>'Las credenciales ingresadas no son válidas'
            ], Response::HTTP_BAD_REQUEST);//400
            }
        }   
    }
    
    #[Route('/api/v1/acceso/registro', methods:['POST'])]
    public function registro(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse {
    
        $data = json_decode($request->getContent(), true);
        if(!isset($data['nombre'])){
            return $this->json([
                'estado'=>'error',
                'mensaje'=>'El campo nombre es obligatorio'
            ],200);
        }     
        if(!isset($data['correo'])){
            return $this->json([
                'estado'=>'error',
                'mensaje'=>'El campo correo es obligatorio'
            ],200);
        }
        if(!isset($data['password'])){
            return $this->json([
                'estado'=>'error',
                'mensaje'=>'El campo password es obligatorio'
            ],200);
        }
        $existe= $this->em->getRepository(Usuarios::class)->findOneBy(['correo'=>$data['correo']]);
        if($existe){
            return $this->json([
                'estado'=>'error',
                'mensaje'=>"El correo {$data['correo']} ya esta siendo usado por otro usuario"
            ],200);
        }
        $entity = new Usuarios(); #p2gHNiENUw
        $entity->setNombre($data['nombre']);
        $entity->setCorreo($data['correo']);
        $entity->setPassword($passwordHasher->hashPassword($entity, $data['password']));
        $entity->setRoles(['ROLE_USER']);
        $this->em->persist($entity);
        $this->em->flush();
        return $this->json([
            'estado'=>'ok',
            'mensaje'=>"Se creó el registro cexitosamente"
        ], Response::HTTP_CREATED);//201
        
    } 
}

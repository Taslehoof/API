<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class EjemploController extends AbstractController
{
    #[Route('/')]
    public function home(): JsonResponse
    {
        return $this->json([
            'estado' => 'ok',
            'mensaje' => 'Hola mundo desde una API Rest con Symfony',
        ], 200);
    }
    

    #[Route('/ejemplo',methods: ['GET'])]
    public function metodo_get(): JsonResponse
    {
        return $this->json([
            'estado' => 'ok',
            'mensaje' => 'metodo GET',
        ]);
    }
    
    #[Route('/ejemplo/{id}',methods: ['GET'])]
    public function metodo_get_con_parametro(int $id): JsonResponse
    {
        return $this->json([
            'estado' => 'ok',
            'mensaje' => 'metodo GET | id='.$id,
        ]);
    }
    
    #[Route('/ejemplo-query-string',methods: ['GET'])]
    public function metodo_get_query_string(Request $request): JsonResponse
    {
        return $this->json([
            'estado' => 'ok',
            'mensaje' => 'metodo GET | id='.$request->query->get('id'),
        ]);
    }
    
    #[Route('/ejemplo',methods: ['POST'])]
    public function metodo_post(request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        print_r($data);exit();
        return $this->json([
            'estado' => 'ok',
            'mensaje' => 'metodo POST',
        ]);
    }

    /*#[Route('/ejemplo',methods: ['POST'])]
    public function metodo_post(): JsonResponse
    {
        return $this->json([
            'estado' => 'ok',
            'mensaje' => 'metodo POST',
        ]);
    }*/
    
    #[Route('/ejemplo',methods: ['PUT'])]
    public function metodo_put(): JsonResponse
    {
        return $this->json([
            'estado' => 'ok',
            'mensaje' => 'metodo PUT',
        ]);
    }
    
    #[Route('/ejemplo/{id}',methods: ['PUT'])]
    public function metodo_put_con_parametro(int $id): JsonResponse
    {
        return $this->json([
            'estado' => 'ok',
            'mensaje' => 'metodo PUT | id='.$id,
        ]);
    }
    
    #[Route('/ejemplo',methods: ['DELETE'])]
    public function metodo_delete(): JsonResponse
    {
        return $this->json([
            'estado' => 'ok',
            'mensaje' => 'metodo DELETE',
        ]);
    }
    
    #[Route('/ejemplo/{id}',methods: ['DELETE'])]
    public function metodo_delete_con_parametro(int $id): JsonResponse
    {
        return $this->json([
            'estado' => 'ok',
            'mensaje' => 'metodo DELETE | id='.$id,
        ]);
    }
}

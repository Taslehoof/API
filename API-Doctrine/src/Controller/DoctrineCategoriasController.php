<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Entity\Producto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class DoctrineCategoriasController extends AbstractController{
    private $em;
    
    public function __construct(EntityManagerInterface $em){
        $this->em=$em;
    }

    #[Route('/api/v1/doctrine/categorias', methods:['GET'])]
    public function metodo_get(): JsonResponse{
        $datos=$this->em->getRepository(Categoria::class)->findBy(array(), array("id"=>"desc"));
        return $this->json($datos);
    }
        
    #[Route('/api/v1/doctrine/categorias/{id}', methods:['GET'])]
    public function metodo_get_con_parametros(int $id): JsonResponse{
        $datos=$this->em->getRepository(Categoria::class)->find($id);
        if(!$datos){
            return $this->json([
                'estado'=>'error',
                'mensaje'=>'La URL no esta disponible en este momento'
            ], 404);
        }
        return $this->json($datos);
    }

    #[Route('/api/v1/doctrine/categorias', methods:['POST'])]
    public function metodo_post(Request $request, SluggerInterface $slugger): JsonResponse{
        
        $data =  json_decode($request->getContent(), true);
        if(!isset($data['nombre'])){
            return $this->json([
                'estado'=>'error',
                'mensaje'=>'El campo nombre es obligatorio'
            ], 200);
        }
        $entity = new Categoria();
        $entity->setNombre($data['nombre']);
        $entity->setSlug($slugger->slug(strtolower($data['nombre'])));
        $this->em->persist($entity);
        $this->em->flush();
        return $this->json([
            'estado'=>'ok',
            'mensaje'=>'se creó el registro exitosamente'
         ], 201);
    }
    
    #[Route('/api/v1/doctrine/categorias/{id}', methods:['PUT'])]
    public function metodo_put(int $id, Request $request, SluggerInterface $slugger): JsonResponse{
        $datos=$this->em->getRepository(Categoria::class)->find($id);
        if(!$datos){
            return $this->json([
                'estado'=>'error',
                'mensaje'=>'La URL no esta disponible en este momento'
            ], 404);
        }
        
        $data =  json_decode($request->getContent(), true);
        if(!isset($data['nombre'])){
            return $this->json([
                'estado'=>'error',
                'mensaje'=>'El campo nombre es obligatorio'
            ], 200);
        }
        $datos->setNombre($data['nombre']);
        $datos->setSlug($slugger->slug(strtolower($data['nombre'])));
        #$this->em->persist($entity);
        $this->em->flush();
        return $this->json([
            'estado'=>'ok',
            'mensaje'=>'se modificó el registro exitosamente'
         ], 200);
    }

    #[Route('/api/v1/doctrine/categorias/{id}', methods:['DELETE'])]
    public function metodo_delete(int $id, Request $request, SluggerInterface $slugger): JsonResponse{
        $datos=$this->em->getRepository(Categoria::class)->find($id);
        if(!$datos){
            return $this->json([
                'estado'=>'error',
                'mensaje'=>'La URL no esta disponible en este momento'
            ], 404);
        }

        $producto = $this->em->getRepository(Producto::class)->findBy(array('categoria'=>$id),array());
        if($producto){
            return new JsonResponse([
                'estado'=>'error',
                'mensaje'=>'No se pudo completar la petición, ocurrió un error inesperado'
            ],400);
        } else {
            $this->em->remove($datos);
            $this->em->flush();
            return $this->json([
                'estado'=>'ok',
                'mensaje'=>'Se eliminó el registro exitosamente'
            ]);

        }
    }
}

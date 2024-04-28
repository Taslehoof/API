<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Entity\ProductoFoto;
use App\Entity\Categoria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class DoctrineProductosController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/api/v1/doctrine/productos', methods: ['GET'])]
    public function metodo_get(): JsonResponse
    {
        $datos = $this->em->getRepository(Producto::class)->findBy(array(), array('id' => 'desc'));
        return $this->json($datos);
    }

    #[Route('/api/v1/doctrine/productos/{id}', methods: ['GET'])]
    public function metodo_get_con_parametros(int $id): JsonResponse
    {
        $datos = $this->em->getRepository(Producto::class)->find($id);
        if (!$datos) {
            return $this->json([
                'estado' => 'error',
                'mensaje' => 'La URL no esta disponible en este momento'
            ], 404);
        }
        return $this->json($datos);
    }

    #[Route('/api/v1/doctrine/productos', methods: ['POST'])]
    public function metodo_post(Request $request, SluggerInterface $slugger): JsonResponse
    {

        $data = json_decode($request->getContent(), true);
        if (!isset($data['nombre'])) {
            return $this->json([
                'estado' => 'error',
                'mensaje' => 'El campo nombre es obligatorio'
            ], 200);
        }
        if (!isset($data['precio'])) {
            return $this->json([
                'estado' => 'error',
                'mensaje' => 'El campo precio es obligatorio'
            ], 200);
        }
        if (!is_numeric($data['precio'])) {
            return $this->json([
                'estado' => 'error',
                'mensaje' => 'El campo precio debe ser numerico'
            ], 200);
        }
        if (!isset($data['stock'])) {
            return $this->json([
                'estado' => 'error',
                'mensaje' => 'El campo stock es obligatorio'
            ], 200);
        }
        if (!is_numeric($data['stock'])) {
            return $this->json([
                'estado' => 'error',
                'mensaje' => 'El campo stock debe ser numerico'
            ], 200);
        }
        if (!isset($data['descripcion'])) {
            return $this->json([
                'estado' => 'error',
                'mensaje' => 'El campo stock descripcion es obligatorio'
            ], 200);
        }
        if (!isset($data['categoria_id'])) {
            return $this->json([
                'estado' => 'error',
                'mensaje' => 'El campo categoria_id es obligatorio'
            ], 200);
        }
        $categoria = $this->em->getRepository(Categoria::class)->find($data['categoria_id']);
        if (!$categoria) {
            return $this->json([
                'estado' => 'error',
                'mensaje' => 'El campo categoria_id indicado no es valido'
            ], 200);
        }
        $entity = new Producto();
        $entity->setNombre($data['nombre']);
        $entity->setSlug($slugger->slug(strtolower($data['nombre'])));
        $entity->setPrecio($data['precio']);
        $entity->setStock($data['stock']);
        $entity->setDescripcion($data['descripcion']);
        $entity->setCategoria($categoria);
        $this->em->persist($entity);
        $this->em->flush();
        return $this->json([
            'estado' => 'ok',
            'mensaje' => 'se creó el registro exitosamente'
        ], 201);
    }
    
    #[Route('/api/v1/doctrine/productos/{id}', methods:['PUT'])]
    public function metodo_put(int $id, Request $request, SluggerInterface $slugger): JsonResponse{
        $datos=$this->em->getRepository(Producto::class)->find($id);
        if(!$datos){
            return $this->json([
                'estado'=>'error',
                'mensaje'=>'La URL no esta disponible en este momento'
            ], 404);
        }
        
        $data = json_decode($request->getContent(), true);
        if (!isset($data['nombre'])) {
            return $this->json([
                'estado' => 'error',
                'mensaje' => 'El campo nombre es obligatorio'
            ], 200);
        }
        if (!isset($data['precio'])) {
            return $this->json([
                'estado' => 'error',
                'mensaje' => 'El campo precio es obligatorio'
            ], 200);
        }
        if (!is_numeric($data['precio'])) {
            return $this->json([
                'estado' => 'error',
                'mensaje' => 'El campo precio debe ser numerico'
            ], 200);
        }
        if (!isset($data['stock'])) {
            return $this->json([
                'estado' => 'error',
                'mensaje' => 'El campo stock es obligatorio'
            ], 200);
        }
        if (!is_numeric($data['stock'])) {
            return $this->json([
                'estado' => 'error',
                'mensaje' => 'El campo stock debe ser numerico'
            ], 200);
        }
        if (!isset($data['descripcion'])) {
            return $this->json([
                'estado' => 'error',
                'mensaje' => 'El campo stock descripcion es obligatorio'
            ], 200);
        }
        if (!isset($data['categoria_id'])) {
            return $this->json([
                'estado' => 'error',
                'mensaje' => 'El campo categoria_id es obligatorio'
            ], 200);
        }

        $datos->setNombre($data['nombre']);
        $datos->setSlug($slugger->slug(strtolower($data['nombre'])));
        $datos->setPrecio($data['precio']);
        $datos->setStock($data['stock']);
        $datos->setDescripcion($data['descripcion']);
        #$datos->setCategoria($categoria);
        #$this->em->persist($entity);
        $this->em->flush();
        return $this->json([
            'estado'=>'ok',
            'mensaje'=>'se modificó el registro exitosamente'
         ], 200);
    }
    
    #[Route('/api/v1/doctrine/productos/{id}', methods:['DELETE'])]
    public function metodo_delete(int $id): JsonResponse{
        $datos=$this->em->getRepository(Producto::class)->find($id);
        if(!$datos){
            return $this->json([
                'estado'=>'error',
                'mensaje'=>'La URL no esta disponible en este momento'
            ], 404);
        }

        $producto = $this->em->getRepository(ProductoFoto::class)->findBy(array('producto'=>$id),array());
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

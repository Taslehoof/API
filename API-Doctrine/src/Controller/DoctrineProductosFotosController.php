<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Entity\ProductoFoto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DoctrineProductosFotosController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em=$em;
    }    

    #[Route('/api/v1/doctrine/productos/fotos/{id}', methods:['GET'])]
    public function metodo_get(int $id): JsonResponse{
        
        $producto= $this->em->getRepository(Producto::class)->find($id);
        if(!$producto){
            return $this->json([
                'estado' => 'error',
                'mensaje' => 'La URL no esta disponible en este momento',
            ], 404);
        }
        
        $datos= $this->em->getRepository(ProductoFoto::class)->findBy(array('producto'=>$id),array('id'=>'DESC'));
        $arreglo = [];
        foreach($datos as $dato){
            $arreglo[]=['id'=>$dato->getId(), 'foto'=>$dato->getFoto(), 'producto_id'=>$dato->getProducto()->getId()];
        }
        return $this->json($arreglo);
    }

    #[Route('/api/v1/doctrine/productos/fotos', methods:['POST'])]
    public function metodo_post(Request $request): JsonResponse{
        
        $foto = $request->files->get('foto');
        if($foto){
            $newFilename = time().'.'.$foto->guessExtension();
            try{
                $foto->move($this->getParameter('productos_directory'), $newFilename);

                $producto = $this->em->getRepository(Producto::class)->find($request->request->get('producto_id'));
                if(!$producto){
                    return new JsonResponse([
                        'estado'=>'error',
                        'mensaje'=>'No se pudo completar la petición, ocurrió un error inesperado'
                    ], 400);
                }
                $entity = new ProductoFoto();
                $entity->setFoto($newFilename);
                $entity->setProducto($producto);
                $this->em->persist($entity);
                $this->em>flush();
                return $this->json([
                    'estado'=>'ok',
                    'mensaje'=>'Se creo el registro exitosamente asociado al producto_id'.$request->request->get('producto_id')
                ],200);
            } catch (FileException $e){
                return $this->json([
                    'estado'=>'error',
                    'mensaje'=>'Ups ocurrio un error al intentar subir el archivo'
                ],400);
            }
        }
    }

    #[Route('/api/v1/doctrine/productos/fotos-descargar/{id}', methods:['GET'])]
    public function descargar(int $id): BinaryFileResponse{
        $entity = $this->em->getrepository(Producto::class)->find($id);
        if(!$entity){
            return $this->json([
                'estado'=>'error',
                'mensaje'=>'La URL no esta dipoblible por el momento'
            ],404);
        }
        $ruta = getcwd();
        return $this->file("{$ruta}/uploads/productos/{$entity->getFoto()}");
    }
    #[Route('/api/v1/doctrine/productos/foto/{id}', methods:['DELETE'])]
    public function metodo_delete(int $id): JsonResponse{
    
        $entity = $this->em->getRepository(ProductoFoto::class)->find($id);
        if(!$entity){
            return $this->json([
                'estado'=>'error',
                'mensaje'=>'La URL no esta dipoblible por el momento'
            ],404);
        } 
        unlink(getcwd().'/uploads/productos/'.$entity->getFoto());
        $this->em->remove($entity);
        $this->em->flush();
        return $this->json([
                'estado'=>'ok',
                'mensaje'=>'Se eliminó el registro exitosamente'
        ]);
    }
}

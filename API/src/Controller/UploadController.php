<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class UploadController extends AbstractController
{
    #[Route('/upload', methods: ['POST'])]
    public function metodo_post(Request $request): JsonResponse
    {
        $foto = $request->files->get('foto');
        if ($foto){
                $newFilename= time().'.'.$foto->guessExtension();
                try{
                    $foto->move(
                        $this->getParameter('productos_directory'),
                        $newFilename
                    );
                    return $this->json([
                        'estado'=>'ok',
                        'mensaje'=>'Se creo el registro exitosamente asociado al producto_id '.$request->request->get('producto_id'),
                    ],200);
                } catch (\Throwable $th){
                    return $this->json([
                        'estado'=>'error',
                        'mensaje'=>'Ups ocurrio un error al intentar subir el archivo'
                    ],400);
                }
        }
    }
}

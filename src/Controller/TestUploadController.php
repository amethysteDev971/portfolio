<?php

// src/Controller/TestUploadController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TestUploadController extends AbstractController
{
   
    #[Route('/test-upload', name: 'test_upload', methods: ['POST'])]
    public function testUpload(Request $request): JsonResponse
    {
        $file = $request->files->get('croppedImage');
        if (!$file) {
            return new JsonResponse(['error' => 'No file'], 400);
        }

        // Chemin temporaire dans public/tmp
        $targetDir = $this->getParameter('kernel.project_dir').'/public/tmp';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $filename = uniqid('crop_', true).'.'.$file->guessExtension();
        $file->move($targetDir, $filename);

        // On renvoie juste l’URL brute
        return new JsonResponse([
            'url' => '/tmp/'.$filename
        ]);
    }
}

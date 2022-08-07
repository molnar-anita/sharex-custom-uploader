<?php

namespace App\Controller\Api;

use App\Exception\FileNotExistsException;
use App\Service\FileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DownloadFileController extends AbstractController {

    #[Route('/api/file/{uuid}/{fileName}', name: 'api.file.download', methods: ['GET'])]
    public function downloadFile(
        string      $uuid,
        string      $fileName,
        FileService $fileService
    ): Response {
        try {
            return $fileService->downloadFile($uuid, $fileName);
        } catch (FileNotExistsException $e) {
            return new Response(status: 404);
        }
    }
}

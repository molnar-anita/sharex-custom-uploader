<?php

namespace App\Controller\Api;

use App\Exception\FileNotExistsException;
use App\Service\FileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DownloadFileController extends AbstractController {

    #[Route('/api/file/{uuid}/{fileName}', name: 'api.file.download.uuid', methods: ['GET'])]
    public function downloadFileByUUID(
        string      $uuid,
        string      $fileName,
        FileService $fileService
    ): Response {
        try {
            return $fileService->downloadFileByUUID($uuid, $fileName);
        } catch (FileNotExistsException $e) {
            return new Response(status: 404);
        }
    }

    #[Route('/files/{base64}/{fileName}', name: 'api.file.download.base64', methods: ['GET'])]
    public function downloadFileByBase64Id(
        string      $base64,
        string      $fileName,
        FileService $fileService
    ): Response {
        try {
            return $fileService->downloadFileByBase64Id($base64, $fileName);
        } catch (FileNotExistsException $e) {
            return new Response(status: 404);
        }
    }
}

<?php

namespace App\Controller\Api;

use App\Exception\FileNotExistsException;
use App\Exception\UserHasNoPermissionOnFileException;
use App\Service\FileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteFileController extends AbstractController {

    #[Route('/api/file/{uuid}/{fileName}/{deleteToken}', name: 'api.file.delete', methods: ['DELETE', 'GET'])]
    public function deleteFile(
        string      $uuid,
        string      $fileName,
        string      $deleteToken,
        FileService $fileService
    ): Response {
        try {
            $fileService->removeFile($uuid, $fileName, $deleteToken);
        } catch (FileNotExistsException $e) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        } catch (UserHasNoPermissionOnFileException $e) {
            return new Response(status: Response::HTTP_FORBIDDEN);
        }

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}

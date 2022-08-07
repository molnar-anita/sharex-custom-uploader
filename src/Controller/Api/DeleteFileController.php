<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Exception\FileNotExistsException;
use App\Exception\UserHasNoPermissionOnFileException;
use App\Middleware\CheckApiKeyMiddleware;
use App\Service\FileService;
use Kafkiansky\SymfonyMiddleware\Attribute\Middleware;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteFileController extends AbstractController {

    #[Middleware([CheckApiKeyMiddleware::class])]
    #[Route('/api/file/{uuid}/{fileName}', name: 'api.file.delete', methods: ['DELETE'])]
    public function deleteFile(
        string      $uuid,
        string      $fileName,
        FileService $fileService,
        User        $user
    ): Response {
        try {
            $fileService->removeFile($uuid, $fileName, $user);
        } catch (FileNotExistsException $e) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        } catch (UserHasNoPermissionOnFileException $e) {
            return new Response(status: Response::HTTP_FORBIDDEN);
        }

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}

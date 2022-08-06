<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Middleware\CheckApiKeyMiddleware;
use App\Service\FileService;
use Kafkiansky\SymfonyMiddleware\Attribute\Middleware;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UploadFileController extends AbstractController {

    #[Middleware([CheckApiKeyMiddleware::class])]
    #[Route('/api/file', name: 'api.file.create', methods: ['POST'])]
    public function uploadFile(
        Request     $request,
        User        $user,
        FileService $fileService
    ): Response {

        if (!$request->files->has('file'))
            return new Response(status: Response::HTTP_BAD_REQUEST);

        /** @var UploadedFile $file */
        $file = $request->files->get('file');

        if (is_null($file))
            return new Response(status: Response::HTTP_BAD_REQUEST);

        return $this->json(['name' => $fileService->saveFile($file, $user)->getName()]);
    }
}

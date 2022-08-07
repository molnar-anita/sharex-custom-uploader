<?php

namespace App\Controller\Api;

use App\Entity\File;
use App\Entity\User;
use App\Middleware\CheckApiKeyMiddleware;
use App\Service\FileService;
use Kafkiansky\SymfonyMiddleware\Attribute\Middleware;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

        /** @var UploadedFile $newFile */
        $newFile = $request->files->get('file');

        if (is_null($newFile) || !$newFile->isValid())
            return new Response(status: Response::HTTP_BAD_REQUEST);

        $file = $fileService->saveFile($newFile, $user);

        return $this->json($this->generateResponse($file));
    }

    protected function generateResponse(File $file): array {
        return [
            'uuid' => $file->getUuid(),
            'fileName' => $file->getName(),
            'mime' => $file->getMime(),
            'downloadUrl' =>
                $this->generateUrl(
                    'api.file.download',
                    [
                        'uuid' => $file->getUuid(),
                        'fileName' => $file->getName()
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            'removeUrl' =>
                $this->generateUrl(
                    'api.file.delete',
                    [
                        'uuid' => $file->getUuid(),
                        'fileName' => $file->getName()
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
        ];
    }
}

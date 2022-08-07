<?php

namespace App\Controller\Api;

use App\Entity\File;
use App\Entity\User;
use App\Middleware\CheckApiKeyMiddleware;
use App\Service\FileService;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
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

        $file = $fileService->saveFile($newFile, $user, $this->determineExpirationDateTime($request));

        return $this->json($this->generateResponse($file));
    }

    private function determineExpirationDateTime(Request $request): ?DateTimeImmutable {
        //TODO: Make configurable
        $expireInMinutes = 60 * 24 * 30; // 30 days

        if ($request->headers->has('X-Expire-In-Minutes')) {
            $headerField = $request->headers->get('X-Expire-In-Minutes');
            if (is_numeric($headerField)) {
                $minutes = intval($headerField);
                if ($minutes >= 0 && $minutes <= 60 * 24 * 365)
                    $expireInMinutes = $minutes;
            }
        }

        return $expireInMinutes === 0 ? null : (new DateTimeImmutable())->add(new DateInterval("PT{$expireInMinutes}M"));
    }

    private function generateResponse(File $file): array {
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
                        'fileName' => $file->getName(),
                        'deleteToken' => $file->getDeleteToken()
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            'expireIn' => $file->getExpireIn()->format(DateTimeInterface::ATOM)
        ];
    }
}

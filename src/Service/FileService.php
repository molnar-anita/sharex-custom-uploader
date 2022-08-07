<?php

namespace App\Service;

use App\Entity\File;
use App\Entity\User;
use App\Exception\FileNotExistsException;
use App\Exception\UserHasNoPermissionOnFileException;
use App\Repository\FileRepository;
use App\Repository\LocalFileStorageRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class FileService {

    public function __construct(
        private readonly FileRepository             $fileRepository,
        private readonly LocalFileStorageRepository $storage
    ) {}

    public function saveFile(UploadedFile $uploadedFile, User $user): File {
        $randomName = $this->storage->saveContentWithRandomName($uploadedFile->getContent());

        //TODO: Sanitize file name
        $file = (new File())
            ->setName($uploadedFile->getClientOriginalName())
            ->setAccessOnce(false)
            ->setMime($uploadedFile->getMimeType())
            ->setPath($randomName)
            ->setUser($user);

        $this->fileRepository->add($file, true);

        return $file;
    }

    public function downloadFile(string $uuid, string $fileName): Response {
        $file = $this->fileRepository->findOneBy(['uuid' => $uuid, 'name' => $fileName]);

        if (is_null($file))
            throw new FileNotExistsException($fileName);

        $response = (new Response(
            content: $this->storage->getContent($file->getPath()),
            status: Response::HTTP_OK,
            headers: [
                'Content-Type' => $file->getMime(),
                'Content-Disposition' => HeaderUtils::makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $file->getName())
            ]
        ));

        if ($file->isAccessOnce()) {
            $this->fileRepository->remove($file, true);
            $this->storage->delete($file->getPath());
        }

        return $response;
    }

    public function removeFile(string $uuid, string $fileName, string $deleteToken): void {
        $file = $this->fileRepository->findOneBy(['uuid' => $uuid, 'name' => $fileName]);

        if (is_null($file))
            throw new FileNotExistsException($fileName);

        if ($file->getDeleteToken() !== $deleteToken)
            throw new UserHasNoPermissionOnFileException($fileName, 'delete');

        $this->fileRepository->remove($file, true);
        $this->storage->delete($file->getPath());
    }
}

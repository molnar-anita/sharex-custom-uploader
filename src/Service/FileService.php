<?php

namespace App\Service;

use App\Entity\File;
use App\Entity\User;
use App\Exception\FileNotExistsException;
use App\Exception\UserHasNoPermissionOnFileException;
use App\Repository\FileRepository;
use App\Repository\LocalFileStorageRepository;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Uid\Uuid;

class FileService {

    public function __construct(
        private readonly FileRepository             $fileRepository,
        private readonly LocalFileStorageRepository $storage
    ) {}

    public function saveFile(UploadedFile $uploadedFile, User $user): File {
        $randomName = $this->storage->saveWithRandomName($uploadedFile);

        //TODO: Sanitize file name
        $file = (new File())
            ->setUuid(Uuid::v4())
            ->setName($uploadedFile->getClientOriginalName())
            ->setAccessOnce(false)
            ->setMime($uploadedFile->getMimeType())
            ->setPath($randomName)
            ->setUser($user);

        $this->fileRepository->add($file, true);

        return $file;
    }

    public function downloadFile(string $uuid, string $fileName): BinaryFileResponse {
        $file = $this->fileRepository->findOneBy(['uuid' => $uuid, 'name' => $fileName]);

        if (is_null($file))
            throw new FileNotExistsException($fileName);

        return (new BinaryFileResponse(
            $this->storage->getPath($file->getPath()),
            Response::HTTP_OK,
            ['Content-Type' => $file->getMime()]
        ))->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $file->getName()
        );
    }

    public function removeFile(string $uuid, string $fileName, User $user): void {
        $file = $this->fileRepository->findOneBy(['uuid' => $uuid, 'name' => $fileName]);

        if (is_null($file))
            throw new FileNotExistsException($fileName);

        if (!$file->getUser()->equals($user))
            throw new UserHasNoPermissionOnFileException($fileName, 'delete', $user);

        $this->fileRepository->remove($file, true);
        $this->storage->delete($file->getPath());
    }
}

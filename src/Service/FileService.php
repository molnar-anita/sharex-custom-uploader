<?php

namespace App\Service;

use App\Entity\File;
use App\Entity\User;
use App\Repository\FileRepository;
use App\Repository\LocalFileStorageRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class FileService {

    public function __construct(
        private readonly FileRepository             $fileRepository,
        private readonly LocalFileStorageRepository $storage
    ) {}

    public function saveFile(UploadedFile $uploadedFile, User $user): File {
        $randomName = $this->storage->saveWithRandomName($uploadedFile);

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
}

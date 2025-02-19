<?php

namespace App\Service;

use App\Entity\File;
use App\Entity\User;
use App\Exception\FileNotExistsException;
use App\Exception\UserHasNoPermissionOnFileException;
use App\Repository\FileRepository;
use App\Repository\LocalFileStorageRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileService {

    public function __construct(
        private readonly FileRepository             $fileRepository,
        private readonly LocalFileStorageRepository $storage,
        private readonly SluggerInterface           $slugger
    ) {}

    public function saveFile(UploadedFile $uploadedFile, User $user, ?DateTimeImmutable $expireIn = null, bool $accessOnce = false): File {
        $randomName = $this->storage->saveContentWithRandomName($uploadedFile->getContent());
        $sluggishNameWithExtension = $this->replaceLastHyphenWithPoint(
            $this->slugger->slug($uploadedFile->getClientOriginalName())
        );

        $file = (new File())
            ->setName($sluggishNameWithExtension)
            ->setAccessOnce(false)
            ->setMime($uploadedFile->getMimeType())
            ->setPath($randomName)
            ->setExpireIn($expireIn)
            ->setAccessOnce($accessOnce)
            ->setUser($user);

        $this->fileRepository->add($file, true);

        return $file;
    }

    private function replaceLastHyphenWithPoint(string $string): string {
        return preg_replace('/(.*)-(.+$)/', '$1.$2', $string);
    }

    public function downloadFileByUUID(string $uuid, string $fileName): Response {
        $file = $this->fileRepository->findOneBy(['uuid' => $uuid, 'name' => $fileName]);

        if (is_null($file))
            throw new FileNotExistsException($fileName);

        return $this->downloadFile($file);
    }

    private function downloadFile(File $file): Response {
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

    public function downloadFileByBase64Id(string $base64Id, string $fileName): Response {
        $file = $this->fileRepository->findOneBy(['id' => base64_decode($base64Id), 'name' => $fileName]);

        if (is_null($file))
            throw new FileNotExistsException($fileName);

        return $this->downloadFile($file);
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

    public function removeExpiredFiles(): int {
        return $this->removeFiles($this->fileRepository->findExpiredFiles());
    }

    private function removeFiles(array $files): int {
        foreach ($files as $file) {
            if ($this->storage->exists($file->getPath())) {
                $this->storage->delete($file->getPath());
            }
            $this->fileRepository->remove($file);
        }

        $this->fileRepository->flush();

        return count($files);
    }

    public function removeOlderFiles(DateTimeImmutable $olderThan): int {
        return $this->removeFiles($this->fileRepository->findOlderFiles($olderThan));
    }

    public function removeOrphanFiles(): int {
        $filesFromStorage = $this->storage->listFiles();
        $filesFromDb = array_map(fn(File $file): string => $file->getPath(), $this->fileRepository->findAll());
        $filesFromDb = array_fill_keys($filesFromDb, $filesFromDb);

        $deletedFilesCount = 0;
        foreach ($filesFromStorage as $file) {
            if (!key_exists($file, $filesFromDb)) {
                $this->storage->delete($file);
                $deletedFilesCount++;
            }
        }

        return $deletedFilesCount;
    }
}

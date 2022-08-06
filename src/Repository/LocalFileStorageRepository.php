<?php

namespace App\Repository;

use App\Entity\File;
use App\Exception\FileAlreadyExistsException;
use App\Exception\UnexpectedException;
use DateTime;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\String\ByteString;

class LocalFileStorageRepository {

    private static $maxRetry = 10;
    private static $storagePath = 'storage/';

    public function __construct(
        private Filesystem      $filesystem,
        private KernelInterface $app
    ) {}

    public function saveWithRandomName(UploadedFile $file): string {
        for ($i = 0; $i < self::$maxRetry; $i++) {
            try {
                $randomFileName = $this->generateRandomFileName();
                $this->save($file, $randomFileName);

                return $randomFileName;
            } catch (FileAlreadyExistsException $e) {
            }
        }
        throw new UnexpectedException("File was tried to save $i times, all failed");
    }

    private function generateRandomFileName(): string {
        return (new DateTime())->format('Y_m_d') . '_' . ByteString::fromRandom(16);
    }

    public function save(UploadedFile $file, string $name): void {
        $path = Path::join($this->app->getProjectDir(), self::$storagePath, $name);

        if ($this->filesystem->exists($path)) {
            throw new FileAlreadyExistsException($name, $path);
        }

        $this->filesystem->dumpFile($path, $file->getContent());
    }

    public function getPath(File $file): void {}

    public function delete(string $name): void {
        $path = Path::join($this->app->getProjectDir(), self::$storagePath, $name);
        
        $this->filesystem->remove($path);
    }
}

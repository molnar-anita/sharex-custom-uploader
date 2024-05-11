<?php

namespace App\Repository;

use App\Exception\FileAlreadyExistsException;
use App\Exception\UnexpectedException;
use DateTime;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\String\ByteString;

class LocalFileStorageRepository {

    private static int $maxRetry = 10;
    private static string $storagePath = 'storage/';

    public function __construct(
        private readonly Filesystem      $filesystem,
        private readonly KernelInterface $app
    ) {}

    public function saveContentWithRandomName(string $content): string {
        for ($i = 0; $i < self::$maxRetry; $i++) {
            try {
                $randomFileName = $this->generateRandomFileName();
                $this->save($content, $randomFileName);

                return $randomFileName;
            } catch (FileAlreadyExistsException $e) {
            }
        }
        throw new UnexpectedException("File was tried to save $i times, all failed");
    }

    private function generateRandomFileName(): string {
        return (new DateTime())->format('Y_m_d_H_i') . '_' . ByteString::fromRandom(16);
    }

    public function save(string $content, string $name): void {
        $path = $this->getPath($name);

        if ($this->filesystem->exists($path)) {
            throw new FileAlreadyExistsException($name, $path);
        }

        $this->filesystem->dumpFile($path, $content);
    }

    public function getPath(string $name): string {
        return Path::join($this->app->getProjectDir(), self::$storagePath, $name);
    }

    public function exists(string $name): bool {
        $path = $this->getPath($name);
        return $this->filesystem->exists($path);
    }

    public function listFiles(): array {
        return array_diff(
            scandir($this->getStorageDirectoryPath()),
            array('..', '.')
        );
    }

    public function getStorageDirectoryPath(): string {
        return Path::join($this->app->getProjectDir(), self::$storagePath);
    }

    public function getContent(string $name): string {
        return file_get_contents($this->getPath($name));
    }

    public function delete(string $name): void {
        $this->filesystem->remove($this->getPath($name));
    }
}

<?php

namespace App\Exception;

class FileAlreadyExistsException extends \Exception {

    public function __construct(
        protected string $fileName,
        protected string $path
    ) {
        parent::__construct("The $path is already exists", previous: $this);
    }

    public function getFileName(): string {
        return $this->fileName;
    }

    public function getPath(): string {
        return $this->path;
    }
}

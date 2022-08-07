<?php

namespace App\Exception;

use Exception;

class FileNotExistsException extends Exception {

    public function __construct(
        protected string $fileName
    ) {
        parent::__construct("The $this->fileName is not exists", previous: $this);
    }

    public function getFileName(): string {
        return $this->fileName;
    }
}

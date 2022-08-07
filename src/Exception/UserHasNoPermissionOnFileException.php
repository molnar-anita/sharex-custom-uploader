<?php

namespace App\Exception;

use Exception;

class UserHasNoPermissionOnFileException extends Exception {

    public function __construct(
        protected string $fileName,
        protected string $action
    ) {
        parent::__construct("User has no permission to perform $action on $fileName");
    }

    public function getFileName(): string {
        return $this->fileName;
    }

    public function getAction(): string {
        return $this->action;
    }
}

<?php

namespace App\Exception;

use App\Entity\User;
use Exception;

class UserHasNoPermissionOnFileException extends Exception {

    public function __construct(
        protected string $fileName,
        protected string $action,
        protected User   $user
    ) {
        $userName = $user->getName();
        parent::__construct("$userName has no permission to perform $action on $fileName");
    }

    public function getFileName(): string {
        return $this->fileName;
    }

    public function getAction(): string {
        return $this->action;
    }

    public function getUser(): User {
        return $this->user;
    }
}

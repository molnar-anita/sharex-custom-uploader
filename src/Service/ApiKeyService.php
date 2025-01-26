<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class ApiKeyService {

    public function __construct(
        private readonly UserRepository $userRepository) {
    }

    public function createNewApiKey(string $name): User {
        $user = new User();

        $user->setName($name);

        $this->userRepository->add($user, true);

        return $user;
    }

    /**
     * @return User[]
     */
    public function getAllApiKeys(): array {
        return $this->userRepository->findAll();
    }

    public function removeApiKey(string $apiKey): void {
        $user = $this->getUserByApiKey($apiKey);

        $this->userRepository->remove($user, true);
    }

    public function getUserByApiKey(string $apiKey): ?User {
        return $this->userRepository->findOneBy(['uuid' => $apiKey]);
    }

    public function removeApiKeyByName(string $name): void {
        $user = $this->getUserByName($name);

        $this->userRepository->remove($user, true);
    }

    public function getUserByName(string $name): ?User {
        return $this->userRepository->findOneBy(['name' => $name]);
    }
}

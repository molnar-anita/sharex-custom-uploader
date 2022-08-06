<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Uid\Uuid;

class ApiKeyService {

    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function createNewApiKey(string $name): User {
        $user = new User();

        $user->setName($name);
        $user->setUuid(Uuid::v4());

        $this->userRepository->add($user, true);

        return $user;
    }

    public function getUserByApiKey(string $apiKey): ?User {
        return $this->userRepository->findOneBy(['uuid' => $apiKey]);
    }

    /**
     * @return User[]
     */
    public function getAllApiKeys(): array {
        return $this->userRepository->findAll();
    }
}

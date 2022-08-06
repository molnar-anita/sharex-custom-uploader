<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;

class UserFactoryService {

    public function __construct(
        private readonly RequestStack  $requestStack,
        private readonly ApiKeyService $apiKeyService
    ) {}

    public function getUserByApiKeyFromHeader(): User {
        $request = $this->requestStack->getCurrentRequest();
        //TODO: Revisit this part, should have the same validation as the App\Middleware\CheckApiKeyMiddleware
        return $this->apiKeyService->getUserByApiKey(explode('App ', $request->headers->get('Authentication'))[1]);
    }
}

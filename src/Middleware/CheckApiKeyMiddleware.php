<?php

namespace App\Middleware;

use App\Service\ApiKeyService;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class CheckApiKeyMiddleware implements MiddlewareInterface {

    public function __construct(
        private readonly ApiKeyService $apiKeyService
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        if (!$request->hasHeader('Authentication') || !$this->checkApiKey($request->getHeaderLine('Authentication') ?? '')) {
            return new Response(status: SymfonyResponse::HTTP_UNAUTHORIZED);
        }
        return $handler->handle($request);
    }

    private function checkApiKey(string $field): bool {
        if (!str_starts_with($field, 'App ')) return false;

        $user = $this->apiKeyService->getUserByApiKey(explode('App ', $field)[1]);

        return !is_null($user);
    }
}

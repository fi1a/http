<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http\Fixtures;

use Fi1a\Http\Middlewares\AbstractMiddleware;
use Fi1a\Http\RequestInterface;
use Fi1a\Http\ResponseInterface;

/**
 * Промежуточное ПО для тестирования
 */
class Middleware extends AbstractMiddleware
{
    /**
     * @inheritDoc
     */
    public function handleRequest(RequestInterface $request): void
    {
    }

    /**
     * @inheritDoc
     */
    public function handleResponse(RequestInterface $request, ResponseInterface $response): void
    {
    }
}

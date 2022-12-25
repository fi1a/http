<?php

declare(strict_types=1);

namespace Fi1a\Http\Middlewares;

use Fi1a\Http\JsonResponseInterface;
use Fi1a\Http\MimeInterface;
use Fi1a\Http\RequestInterface;
use Fi1a\Http\ResponseInterface;

/**
 * Поддержка JSON-ответа
 */
class JsonResponseMiddleware extends AbstractMiddleware
{
    /**
     * @var int
     */
    protected $sort = 10000;

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
        if (!($response instanceof JsonResponseInterface)) {
            return;
        }

        buffer()->clear();
        $response->withoutHeader('Content-Type');
        $response->withHeader('Content-Type', MimeInterface::JSON);
        buffer()->send($request, $response);
        $this->terminate();
    }
}

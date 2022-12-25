<?php

declare(strict_types=1);

namespace Fi1a\Http\Middlewares;

use Fi1a\Http\Output;
use Fi1a\Http\RequestInterface;
use Fi1a\Http\ResponseInterface;
use Fi1a\Http\SetCookie;

/**
 * Перенаправления
 */
class RedirectMiddleware extends AbstractMiddleware
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
        if (!$response->isRedirection()) {
            return;
        }
        $output = new Output(new SetCookie());
        $output->send($request, $response);
        $this->terminate();
    }

    /**
     * Выход
     *
     * @codeCoverageIgnore
     */
    protected function terminate(): void
    {
        exit(0);
    }
}

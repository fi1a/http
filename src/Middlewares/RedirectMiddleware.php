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
    public function handleRequest(RequestInterface $request, callable $next): RequestInterface
    {
        return $request;
    }

    /**
     * @inheritDoc
     */
    public function handleResponse(
        RequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {
        if (!$response->isRedirection()) {
            return $next($request, $response);
        }

        $output = new Output(new SetCookie());
        $output->send($response);
        $this->terminate();

        return $next($request, $response);
    }
}

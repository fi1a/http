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
     * @var int
     */
    public static $callTimesRequest = 0;

    /**
     * @var int
     */
    public static $callTimesResponse = 0;

    /**
     * @inheritDoc
     */
    public function handleRequest(RequestInterface $request, callable $next): RequestInterface
    {
        static::$callTimesRequest++;

        return parent::handleRequest($request, $next);
    }

    /**
     * @inheritDoc
     */
    public function handleResponse(
        RequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {
        static::$callTimesResponse++;

        return parent::handleResponse($request, $response, $next);
    }
}

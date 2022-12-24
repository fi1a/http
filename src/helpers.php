<?php

declare(strict_types=1);

use Fi1a\Http\Http;
use Fi1a\Http\HttpInterface;
use Fi1a\Http\RequestInterface;
use Fi1a\Http\Response;
use Fi1a\Http\ResponseInterface;
use Fi1a\Http\SessionHandler;
use Fi1a\Http\SessionStorage;

/**
 * Хелпер для HttpInterface
 */
function http(): HttpInterface
{
    /** @var HttpInterface|null $http */
    static $http = null;
    if (is_null($http)) {
        /** @psalm-suppress InvalidArgument */
        $request = Http::createRequestWithGlobals(
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES,
            $_SERVER
        );
        $http = new Http(
            $request,
            new SessionStorage(new SessionHandler()),
            new Response(
                ResponseInterface::HTTP_OK,
                null,
                $request
            )
        );
    }

    return $http;
}

/**
 * Хелпер для запроса
 */
function request(?RequestInterface $request = null): RequestInterface
{
    return http()->request($request);
}

/**
 * Хелпер для ответа
 */
function response(?ResponseInterface $response = null): ResponseInterface
{
    return http()->response($response);
}

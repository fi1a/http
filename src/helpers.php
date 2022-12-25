<?php

declare(strict_types=1);

use Fi1a\Http\BufferOutput;
use Fi1a\Http\BufferOutputInterface;
use Fi1a\Http\HeaderCollectionInterface;
use Fi1a\Http\Http;
use Fi1a\Http\HttpInterface;
use Fi1a\Http\JsonResponse;
use Fi1a\Http\JsonResponseInterface;
use Fi1a\Http\RedirectResponse;
use Fi1a\Http\RequestInterface;
use Fi1a\Http\Response;
use Fi1a\Http\ResponseInterface;
use Fi1a\Http\SessionStorage;
use Fi1a\Http\SessionStorageInterface;
use Fi1a\Http\SetCookie;
use Fi1a\Http\UriInterface;

/**
 * Хелпер для HttpInterface
 *
 * @codeCoverageIgnore
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
            new SessionStorage(),
            new Response(
                ResponseInterface::HTTP_OK,
                null,
                $request
            ),
            new BufferOutput(new SetCookie())
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

/**
 * Хелпер для сессии
 */
function session(?SessionStorageInterface $session = null): SessionStorageInterface
{
    return http()->session($session);
}

/**
 * Хелпер для сессии
 */
function buffer(?BufferOutputInterface $buffer = null): BufferOutputInterface
{
    return http()->buffer($buffer);
}

/**
 * Возвращает перенаправление
 *
 * @param string|UriInterface $location
 * @param HeaderCollectionInterface|string[]|string[][] $headers
 */
function redirect($location = null, ?int $status = null, $headers = []): RedirectResponse
{
    $redirect = new RedirectResponse();
    if (!is_null($location)) {
        $redirect->to($location, $status, $headers);
    }

    return $redirect;
}

/**
 * JSON ответ
 *
 * @param mixed $data
 * @param HeaderCollectionInterface|string[]|string[][] $headers
 */
function json($data = null, ?int $status = null, $headers = []): JsonResponseInterface
{
    $json = new JsonResponse();
    if (!is_null($data)) {
        $json->data($data, $status, $headers);
    }

    return $json;
}

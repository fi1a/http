<?php

declare(strict_types=1);

use Fi1a\Http\BufferOutputInterface;
use Fi1a\Http\HeaderCollectionInterface;
use Fi1a\Http\HttpInterface;
use Fi1a\Http\JsonResponse;
use Fi1a\Http\JsonResponseInterface;
use Fi1a\Http\RedirectResponse;
use Fi1a\Http\RedirectResponseInterface;
use Fi1a\Http\RequestInterface;
use Fi1a\Http\ResponseInterface;
use Fi1a\Http\Session\SessionStorageInterface;
use Fi1a\Http\UriInterface;

/**
 * Хелпер для HttpInterface
 *
 * @codeCoverageIgnore
 */
function http(): HttpInterface
{
    /** @var HttpInterface $instance */
    $instance = di()->get(HttpInterface::class);

    return $instance;
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
 * Хелпер для буферизированного вывода
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
function redirect($location = null, ?int $status = null, $headers = []): RedirectResponseInterface
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

<?php

declare(strict_types=1);

namespace Fi1a\Http\Middlewares;

use Fi1a\Http\RequestInterface;
use Fi1a\Http\ResponseInterface;

/**
 * Промежуточное ПО
 */
interface MiddlewareInterface
{
    /**
     * Возвращает сортировку
     */
    public function getSort(): int;

    /**
     * Устанавливает сортировку
     *
     * @return $this
     */
    public function setSort(int $sort);

    /**
     * Обработчик для запроса
     *
     * @param callable(RequestInterface): RequestInterface $next
     */
    public function handleRequest(RequestInterface $request, callable $next): RequestInterface;

    /**
     * Обработчик для ответа
     *
     * @param callable(RequestInterface, ResponseInterface): ResponseInterface $next
     */
    public function handleResponse(
        RequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface;
}

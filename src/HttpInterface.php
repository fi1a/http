<?php

declare(strict_types=1);

namespace Fi1a\Http;

use Fi1a\Http\Middlewares\MiddlewareCollectionInterface;
use Fi1a\Http\Middlewares\MiddlewareInterface;

/**
 * Менеджер
 */
interface HttpInterface
{
    public const HEAD = 'HEAD';

    public const GET = 'GET';

    public const POST = 'POST';

    public const PUT = 'PUT';

    public const PATCH = 'PATCH';

    public const DELETE = 'DELETE';

    public const PURGE = 'PURGE';

    public const OPTIONS = 'OPTIONS';

    public const TRACE = 'TRACE';

    public const CONNECT = 'CONNECT';

    /**
     * Возвращает или устанавливает экземпляр класса текущего запроса
     */
    public function request(?RequestInterface $request = null): RequestInterface;

    /**
     * Возвращает или устанавливает экземпляр класса ответа
     */
    public function response(?ResponseInterface $response = null): ResponseInterface;

    /**
     * Возвращает объект буферизированного вывода
     */
    public function buffer(?BufferOutputInterface $buffer = null): BufferOutputInterface;

    /**
     * Создание экземпляра класса Request из глобальных переменных
     *
     * @param mixed[] $query
     * @param mixed[] $post
     * @param mixed[] $options
     * @param mixed[] $cookies
     * @param mixed[][] $files
     * @param mixed[] $server
     */
    public static function createRequestWithGlobals(
        array $query,
        array $post,
        array $cookies,
        array $files,
        array $server
    ): RequestInterface;

    /**
     * Возвращает или устанавливает экземпляр класса сессии
     */
    public function session(?SessionStorageInterface $session = null): SessionStorageInterface;

    /**
     * Добавить промежуточное ПО
     *
     * @return $this
     */
    public function withMiddleware(MiddlewareInterface $middleware, ?int $sort = null);

    /**
     * Возвращает коллекцию промежуточного ПО
     */
    public function getMiddlewares(): MiddlewareCollectionInterface;
}

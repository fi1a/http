<?php

declare(strict_types=1);

namespace Fi1a\Http;

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
     * Возвращает экземпляр класса текущего запроса
     */
    public function getRequest(): RequestInterface;

    /**
     * Устанавливает экземпляр класса текущего запроса
     *
     * @return $this
     */
    public function setRequest(RequestInterface $request);

    /**
     * Создание экземпляра класса Request из глобальных переменных
     *
     * @param mixed[] $query
     * @param mixed[] $post
     * @param mixed[] $options
     * @param mixed[] $cookies
     * @param mixed[][] $files
     * @param string[] $server
     */
    public static function createRequestWithGlobals(
        array $query,
        array $post,
        array $options,
        array $cookies,
        array $files,
        array $server
    ): RequestInterface;

    /**
     * Возвращает экземпляр класса сессии
     */
    public function getSession(): SessionStorageInterface;

    /**
     * Устанавливает экземпляр класса сессии
     *
     * @return $this
     */
    public function setSession(SessionStorageInterface $session);
}

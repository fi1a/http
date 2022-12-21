<?php

declare(strict_types=1);

namespace Fi1a\Http;

/**
 * Запрос
 */
interface RequestInterface
{
    /**
     * @param string|UriInterface $uri
     * @param mixed[] $query
     * @param mixed[] $post
     * @param mixed[] $options
     * @param mixed[] $cookies
     * @param mixed[] $files
     * @param mixed[] $server
     * @param mixed[] $headers
     * @param string|resource|null $content
     */
    public function __construct(
        $uri,
        array $query = [],
        array $post = [],
        array $options = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        array $headers = [],
        $content = null
    );

    /**
     * Возвращает урл с хостом и строку запроса
     *
     * Пример: http://localhost:8080/some/url/?q=1&a=b
     */
    public function getUri(): UriInterface;

    /**
     * Установить урл с хостом и строку запроса
     *
     * @return $this
     */
    public function setUri(UriInterface $uri);
}

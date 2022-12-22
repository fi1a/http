<?php

declare(strict_types=1);

namespace Fi1a\Http;

use Fi1a\Collection\DataType\PathAccessInterface;

/**
 * Запрос
 */
interface RequestInterface
{
    /**
     * @param string|UriInterface $uri
     * @param mixed[]|PathAccessInterface $query
     * @param mixed[]|PathAccessInterface $post
     * @param mixed[] $options
     * @param mixed[] $cookies
     * @param mixed[] $server
     * @param mixed[] $headers
     * @param string|resource|null $content
     */
    public function __construct(
        $uri,
        $query = [],
        $post = [],
        array $options = [],
        array $cookies = [],
        ?UploadFileCollectionInterface $files = null,
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

    /**
     * Устанавливает POST
     *
     * @param mixed[]|PathAccessInterface $post
     *
     * @return $this
     */
    public function setPost($post);

    /**
     * Возвращает POST
     */
    public function getPost(): PathAccessInterface;

    /**
     * Устанавливает GET значения
     *
     * @param PathAccessInterface|mixed[] $query
     *
     * @return $this
     */
    public function setQuery($query);

    /**
     * Возвращает GET значения
     */
    public function getQuery(): PathAccessInterface;

    /**
     * Устанавливает файлы
     *
     * @return $this
     */
    public function setFiles(UploadFileCollectionInterface $files);

    /**
     * Возвращает файлы
     */
    public function getFiles(): UploadFileCollectionInterface;

    /**
     * Устанавливает содержание
     *
     * @param string|resource|null $content
     *
     * @return static
     */
    public function setContent($content);

    /**
     * Возвращает содержание
     *
     * @return resource
     */
    public function getContent();
}

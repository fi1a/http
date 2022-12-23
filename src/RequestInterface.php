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
     * @param mixed[]|PathAccessInterface $options
     * @param string|resource|null $content
     */
    public function __construct(
        $uri,
        $query = [],
        $post = [],
        $options = [],
        ?HttpCookieCollectionInterface $cookies = null,
        ?UploadFileCollectionInterface $files = null,
        ?ServerCollectionInterface $server = null,
        ?HeaderCollectionInterface $headers = null,
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
     * @return $this
     */
    public function setContent($content);

    /**
     * Возвращает содержание
     *
     * @return resource
     */
    public function getContent();

    /**
     * Устанавливает cookies
     *
     * @return $this
     */
    public function setCookies(HttpCookieCollectionInterface $cookies);

    /**
     * Возвращает cookies
     */
    public function getCookies(): HttpCookieCollectionInterface;

    /**
     * Установить заголовки
     *
     * @return $this
     */
    public function setHeaders(HeaderCollectionInterface $headers);

    /**
     * Вернуть заголовки
     */
    public function getHeaders(): HeaderCollectionInterface;

    /**
     * Устанавливает значение SERVER
     *
     * @return $this
     */
    public function setServer(ServerCollectionInterface $server);

    /**
     * Возвращает значение SERVER
     */
    public function getServer(): ServerCollectionInterface;

    /**
     * Возвращает настройки
     */
    public function getOptions(): PathAccessInterface;

    /**
     * Задает настройки
     *
     * @param mixed[]|PathAccessInterface $options
     *
     * @return $this
     */
    public function setOptions($options);
}

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

    /**
     * Возвращает IP адрес клиента
     */
    public function getClientIp(): string;

    /**
     * Возвращает запрошенный файл скрипта
     */
    public function getScriptName(): string;

    /**
     * Устанавливает путь
     *
     * @return $this
     */
    public function setPath(string $url);

    /**
     * Возвращает путь
     *
     * Пример: /some/url/index.php
     */
    public function getPath(): string;

    /**
     * Путь без файла
     *
     * Пример: /some/url/index.php => /some/url/
     */
    public function getBasePath(): string;

    /**
     * Путь без файла со / на конце
     *
     * Пример: /some/url/index.php => /some/url/
     */
    public function getNormalizedBasePath(): string;

    /**
     * Возвращает строку запроса
     *
     * Пример: q=1&a=b
     */
    public function getQueryString(): string;

    /**
     * Устанавливает строку запроса
     *
     * Пример: q=1&a=b
     *
     * @return $this
     */
    public function setQueryString(string $query);

    /**
     * Хост
     */
    public function getHost(): string;

    /**
     * Хост и порт, если он не стандартный
     *
     * Пример: localhost:8080
     */
    public function getHttpHost(): string;

    /**
     * Схема, хост и порт
     *
     * Пример: http://localhost:8080
     */
    public function getSchemeAndHttpHost(): string;

    /**
     * Использован https
     */
    public function isSecure(): bool;

    /**
     * Возвращает схему запроса
     */
    public function getScheme(): string;

    /**
     * Возвращает порт
     */
    public function getPort(): int;

    /**
     * Возвращает пользователя
     */
    public function getUser(): string;

    /**
     * Возвращает пароль
     */
    public function getPassword(): ?string;

    /**
     * Возвращает пользователя и пароль
     *
     * Пример: user:pw, user
     */
    public function getUserInfo(): string;

    /**
     * Возвращает путь и строку запроса
     *
     * Пример: /some/url/?q=1&a=b
     */
    public function getPathAndQuery(): string;

    /**
     * Возвращает урл с хостом и строку запроса
     *
     * Пример: http://localhost:8080/some/url/?q=1&a=b
     */
    public function getUri(): string;

    /**
     * Устанавливает метод
     *
     * @return $this
     */
    public function setMethod(string $method);

    /**
     * Возвращает метод
     */
    public function getMethod(): string;

    /**
     * Определяет метод
     */
    public function isMethod(string $method): bool;

    /**
     * Возвращает тип содержания
     */
    public function getContentType(): string;

    /**
     * Без кеша
     */
    public function isNoCache(): bool;

    /**
     * Возвращает true если запрос XMLHttpRequest
     */
    public function isXmlHttpRequest(): bool;

    /**
     * Возвращает ETags
     *
     * @return string[]
     */
    public function getETags(): array;

    /**
     * Возвращает путь до выполняемого скрипта
     */
    public function getScript(): string;
}

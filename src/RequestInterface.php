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
    public function post(): PathAccessInterface;

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
    public function query(): PathAccessInterface;

    /**
     * Все значения из GET и POST
     */
    public function all(): PathAccessInterface;

    /**
     * Только переданные ключи из GET и POST
     *
     * @param string[] $keys
     */
    public function only(array $keys): PathAccessInterface;

    /**
     * Устанавливает файлы
     *
     * @return $this
     */
    public function setFiles(UploadFileCollectionInterface $files);

    /**
     * Возвращает файлы
     */
    public function files(): UploadFileCollectionInterface;

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
    public function cookies(): HttpCookieCollectionInterface;

    /**
     * Установить заголовки
     *
     * @return $this
     */
    public function setHeaders(HeaderCollectionInterface $headers);

    /**
     * Вернуть заголовки
     */
    public function headers(): HeaderCollectionInterface;

    /**
     * Устанавливает значение SERVER
     *
     * @return $this
     */
    public function setServer(ServerCollectionInterface $server);

    /**
     * Возвращает значение SERVER
     */
    public function server(): ServerCollectionInterface;

    /**
     * Возвращает настройки
     */
    public function options(): PathAccessInterface;

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
    public function clientIp(): string;

    /**
     * Возвращает запрошенный файл скрипта
     */
    public function scriptName(): string;

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
    public function path(): string;

    /**
     * Путь без файла
     *
     * Пример: /some/url/index.php => /some/url/
     */
    public function basePath(): string;

    /**
     * Путь без файла со / на конце
     *
     * Пример: /some/url/index.php => /some/url/
     */
    public function normalizedBasePath(): string;

    /**
     * Возвращает строку запроса
     *
     * Пример: q=1&a=b
     */
    public function queryString(): string;

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
    public function host(): string;

    /**
     * Хост и порт, если он не стандартный
     *
     * Пример: localhost:8080
     */
    public function httpHost(): string;

    /**
     * Схема, хост и порт
     *
     * Пример: http://localhost:8080
     */
    public function schemeAndHttpHost(): string;

    /**
     * Использован https
     */
    public function isSecure(): bool;

    /**
     * Возвращает схему запроса
     */
    public function scheme(): string;

    /**
     * Возвращает порт
     */
    public function port(): int;

    /**
     * Возвращает пользователя
     */
    public function user(): string;

    /**
     * Возвращает пароль
     */
    public function password(): ?string;

    /**
     * Возвращает пользователя и пароль
     *
     * Пример: user:pw, user
     */
    public function userInfo(): string;

    /**
     * Возвращает путь и строку запроса
     *
     * Пример: /some/url/?q=1&a=b
     */
    public function pathAndQuery(): string;

    /**
     * Возвращает урл с хостом и строку запроса
     *
     * Пример: http://localhost:8080/some/url/?q=1&a=b
     */
    public function uri(): string;

    /**
     * Устанавливает метод
     *
     * @return $this
     */
    public function setMethod(string $method);

    /**
     * Возвращает метод
     */
    public function method(): string;

    /**
     * Определяет метод
     */
    public function isMethod(string $method): bool;

    /**
     * Возвращает тип содержания
     */
    public function contentType(): string;

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
    public function eTags(): array;

    /**
     * Возвращает путь до выполняемого скрипта
     */
    public function script(): string;
}

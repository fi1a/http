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
     * Возвращает POST
     */
    public function post(): PathAccessInterface;

    /**
     * Возвращает GET значения
     */
    public function query(): PathAccessInterface;

    /**
     * Все значения из GET, POST, FILES, BODY
     */
    public function all(): PathAccessInterface;

    /**
     * Только переданные ключи из GET, POST, FILES, BODY
     *
     * @param string[] $keys
     */
    public function only(array $keys): PathAccessInterface;

    /**
     * Возвращает файлы
     */
    public function files(): UploadFileCollectionInterface;

    /**
     * Устанавливает содержание
     *
     * @param string|resource|null $rawBody
     *
     * @return $this
     */
    public function setRawBody($rawBody);

    /**
     * Возвращает содержание
     *
     * @return resource
     */
    public function rawBody();

    /**
     * Возвращает преобразованное содержание
     *
     * @return mixed|PathAccessInterface
     */
    public function body();

    /**
     * Устанавливает преобразованное содержание
     *
     * @param mixed $body
     *
     * @return $this
     */
    public function setBody($body);

    /**
     * Возвращает cookies
     */
    public function cookies(): HttpCookieCollectionInterface;

    /**
     * Вернуть заголовки
     */
    public function headers(): HeaderCollectionInterface;

    /**
     * Возвращает значение SERVER
     */
    public function server(): ServerCollectionInterface;

    /**
     * Возвращает опции
     */
    public function options(): PathAccessInterface;

    /**
     * Возвращает IP адрес клиента
     */
    public function clientIp(): string;

    /**
     * Возвращает запрошенный файл скрипта
     */
    public function scriptName(): string;

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

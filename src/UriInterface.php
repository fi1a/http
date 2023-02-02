<?php

declare(strict_types=1);

namespace Fi1a\Http;

use Fi1a\Collection\DataType\PathAccessInterface;

/**
 * URI
 */
interface UriInterface
{
    /**
     * Схема
     */
    public function scheme(): string;

    /**
     * Задать схему
     *
     * @return $this
     */
    public function withScheme(string $scheme);

    /**
     * Использован https
     */
    public function isSecure(): bool;

    /**
     * Компонент информации о пользователе URI
     */
    public function userInfo(): string;

    /**
     * Возвращает имя пользователя
     */
    public function user(): string;

    /**
     * Возвращает пароль
     */
    public function password(): ?string;

    /**
     * Задать информацию о пользователе
     *
     * @return $this
     */
    public function withUserInfo(string $user, ?string $password = null);

    /**
     * Хост
     */
    public function host(): string;

    /**
     * Задать хост
     *
     * @return $this
     */
    public function withHost(string $host);

    /**
     * Порт
     */
    public function port(): ?int;

    /**
     * Задать порт
     *
     * @return $this
     */
    public function withPort(?int $port);

    /**
     * Часть пути URI
     */
    public function path(): string;

    /**
     * Установить часть пути URI
     *
     * @return $this
     */
    public function withPath(string $path);

    /**
     * Урл без файла
     *
     * Пример: /some/url/index.php => /some/url/
     */
    public function basePath(): string;

    /**
     * Урл без файла со / на конце
     *
     * Пример: /some/url/index.php => /some/url/
     */
    public function normalizedBasePath(): string;

    /**
     * Строка запроса в URI
     */
    public function query(): string;

    /**
     * Задать строку запроса URI
     *
     * @return $this
     */
    public function withQuery(string $query);

    /**
     * Массив запроса в URI
     */
    public function queryParams(): PathAccessInterface;

    /**
     * Задать массив запроса в URI
     *
     * @param mixed[]|PathAccessInterface $queryParams
     *
     * @return $this
     */
    public function withQueryParams($queryParams);

    /**
     * Фрагмент URI
     */
    public function fragment(): string;

    /**
     * Задать фрагмент URI
     *
     * @return $this
     */
    public function withFragment(string $fragment);

    /**
     * Возвращает URL
     */
    public function url(): string;

    /**
     * Возвращает URI
     */
    public function uri(): string;

    /**
     * Возвращает путь и строку запроса
     *
     * Пример: /some/url/?q=1&a=b
     */
    public function pathAndQuery(): string;

    /**
     * Компонент полномочий URI
     */
    public function authority(): string;

    /**
     * Возвращает URI с маской на данных авторизации
     */
    public function maskedUri(): string;

    /**
     * Заменить адрес переданным значением
     *
     * @param mixed[]  $variables
     *
     * @return $this
     */
    public function replace(string $uri = '', array $variables = []);
}

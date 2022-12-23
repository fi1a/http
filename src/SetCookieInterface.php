<?php

declare(strict_types=1);

namespace Fi1a\Http;

/**
 * Устанавливает cookie
 */
interface SetCookieInterface
{
    /**
     * Отправляет cookie без URL-кодирования значения
     */
    public function setRaw(HttpCookieInterface $cookie): bool;

    /**
     * Отправляет cookie
     */
    public function set(HttpCookieInterface $cookie): bool;
}

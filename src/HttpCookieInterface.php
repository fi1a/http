<?php

declare(strict_types=1);

namespace Fi1a\Http;

/**
 * Cookie
 */
interface HttpCookieInterface extends CookieInterface
{
    /**
     * Нужно установить cookie или нет
     *
     * @return $this
     */
    public function setNeedSet(bool $needSet = false);

    /**
     * Нужно установить cookie или нет
     */
    public function getNeedSet(): bool;
}

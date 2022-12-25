<?php

declare(strict_types=1);

namespace Fi1a\Http;

/**
 * Устанавливает cookie
 */
class SetCookie implements SetCookieInterface
{
    /**
     * @inheritDoc
     */
    public function setRaw(HttpCookieInterface $cookie): bool
    {
        return $this->doSetCookie('setrawcookie', $cookie);
    }

    /**
     * @inheritDoc
     */
    public function set(HttpCookieInterface $cookie): bool
    {
        return $this->doSetCookie('setcookie', $cookie);
    }

    /**
     * Устанавливает cookie
     */
    protected function doSetCookie(string $function, HttpCookieInterface $cookie): bool
    {
        $cookie->validate();

        return (bool) call_user_func(
            $function,
            $cookie->getName(),
            $cookie->getValue(),
            $cookie->getExpires(),
            $cookie->getPath(),
            $cookie->getDomain(),
            $cookie->getSecure(),
            $cookie->getHttpOnly()
        );
    }
}

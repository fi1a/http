<?php

declare(strict_types=1);

namespace Fi1a\Http;

/**
 * Коллекция cookies
 */
class HttpCookieCollection extends CookieCollection implements HttpCookieCollectionInterface
{
    /**
     * @inheritDoc
     */
    protected function factory($key, $value)
    {
        return new HttpCookie((array) $value);
    }
}

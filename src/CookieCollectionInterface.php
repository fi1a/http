<?php

declare(strict_types=1);

namespace Fi1a\Http;

use Fi1a\Collection\InstanceCollectionInterface;

/**
 * Коллекция cookies
 */
interface CookieCollectionInterface extends InstanceCollectionInterface
{
    /**
     * Возвращает cookie по имени
     *
     * @return CookieInterface|false
     */
    public function getByName(string $name);

    /**
     * Возвращает валидные cookies
     */
    public function getValid(): CookieCollectionInterface;
}

<?php

declare(strict_types=1);

namespace Fi1a\Http;

use Fi1a\Collection\CollectionInterface;

/**
 * Коллекция значений SERVER
 */
interface ServerCollectionInterface extends CollectionInterface
{
    /**
     * Возвращает заголовки
     *
     * @return mixed[]
     */
    public function getHeaders(): array;
}

<?php

declare(strict_types=1);

namespace Fi1a\Http;

/**
 * Сохраняет значение в сессии. После получения значения, стирает его
 */
interface FlushInterface
{
    public function __construct(?SessionStorageInterface $session = null);

    /**
     * Устанавливает значение
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function set(string $key, $value);

    /**
     * Возврашает значение и стирает его
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null);
}

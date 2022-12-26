<?php

declare(strict_types=1);

namespace Fi1a\Http;

/**
 * Ответ с содержанием
 */
interface ContentResponseInterface
{
    /**
     * Установить содержимое
     *
     * @return $this
     */
    public function setContent(string $content);

    /**
     * Вернуть содержимое
     */
    public function getContent(): string;
}

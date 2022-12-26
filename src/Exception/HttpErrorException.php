<?php

declare(strict_types=1);

namespace Fi1a\Http\Exception;

use ErrorException;

/**
 * Http исключения
 */
abstract class HttpErrorException extends ErrorException
{
    /**
     * Возвращает код ответа
     */
    abstract public function getStatus(): int;

    /**
     * Возвращает текст ответа
     */
    public function getReasonPhrase(): ?string
    {
        return null;
    }
}

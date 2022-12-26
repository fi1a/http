<?php

declare(strict_types=1);

namespace Fi1a\Http\Exception;

use Fi1a\Http\ResponseInterface;

/**
 * 500 Internal Server Error
 */
class InternalServerErrorException extends HttpErrorException
{
    /**
     * @inheritDoc
     */
    public function getStatus(): int
    {
        return ResponseInterface::HTTP_INTERNAL_SERVER_ERROR;
    }
}

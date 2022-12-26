<?php

declare(strict_types=1);

namespace Fi1a\Http\Exception;

use Fi1a\Http\ResponseInterface;

/**
 * 400 Bad Request
 */
class BadRequestException extends HttpErrorException
{
    /**
     * @inheritDoc
     */
    public function getStatus(): int
    {
        return ResponseInterface::HTTP_BAD_REQUEST;
    }
}

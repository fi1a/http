<?php

declare(strict_types=1);

namespace Fi1a\Http\Exception;

use Fi1a\Http\ResponseInterface;

/**
 * 404 Page Not Found
 */
class PageNotFoundException extends HttpErrorException
{
    /**
     * @inheritDoc
     */
    public function getStatus(): int
    {
        return ResponseInterface::HTTP_NOT_FOUND;
    }
}

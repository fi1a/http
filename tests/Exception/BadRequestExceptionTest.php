<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http\Exception;

use Fi1a\Http\Exception\BadRequestException;
use Fi1a\Http\ResponseInterface;
use PHPUnit\Framework\TestCase;

/**
 * 400 Bad Request
 */
class BadRequestExceptionTest extends TestCase
{
    /**
     * Статус
     */
    public function testGetStatus()
    {
        $exception = new BadRequestException();

        $this->assertEquals(ResponseInterface::HTTP_BAD_REQUEST, $exception->getStatus());
        $this->assertNull($exception->getReasonPhrase());
    }
}

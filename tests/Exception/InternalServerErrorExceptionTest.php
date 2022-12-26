<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http\Exception;

use Fi1a\Http\Exception\InternalServerErrorException;
use Fi1a\Http\ResponseInterface;
use PHPUnit\Framework\TestCase;

/**
 * 500 Internal Server Error
 */
class InternalServerErrorExceptionTest extends TestCase
{
    /**
     * Статус
     */
    public function testGetStatus()
    {
        $exception = new InternalServerErrorException();

        $this->assertEquals(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, $exception->getStatus());
        $this->assertNull($exception->getReasonPhrase());
    }
}

<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http\Exception;

use Fi1a\Http\Exception\PageNotFoundException;
use Fi1a\Http\ResponseInterface;
use PHPUnit\Framework\TestCase;

class PageNotFoundExceptionTest extends TestCase
{
    /**
     * Статус
     */
    public function testGetStatus()
    {
        $exception = new PageNotFoundException();

        $this->assertEquals(ResponseInterface::HTTP_NOT_FOUND, $exception->getStatus());
        $this->assertNull($exception->getReasonPhrase());
    }
}

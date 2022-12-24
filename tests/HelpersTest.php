<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use Fi1a\Http\HttpInterface;
use Fi1a\Http\RequestInterface;
use Fi1a\Http\ResponseInterface;
use PHPUnit\Framework\TestCase;

use function http;
use function request;

/**
 * Хелперы
 */
class HelpersTest extends TestCase
{
    /**
     * Http хелпер
     */
    public function testHttp(): void
    {
        $this->assertInstanceOf(HttpInterface::class, http());
        $this->assertEquals(http(), http());
    }

    /**
     * Хелпер запроса
     */
    public function testRequest(): void
    {
        $this->assertInstanceOf(RequestInterface::class, request());
    }

    /**
     * Хелпер ответа
     */
    public function testResponse(): void
    {
        $this->assertInstanceOf(ResponseInterface::class, response());
    }
}

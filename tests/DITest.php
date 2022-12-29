<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use Fi1a\Http\BufferOutputInterface;
use Fi1a\Http\RequestInterface;
use Fi1a\Http\ResponseInterface;
use Fi1a\Http\Session\SessionStorageInterface;
use PHPUnit\Framework\TestCase;

/**
 * Тестирование DI определений
 */
class DITest extends TestCase
{
    /**
     * Создание запроса
     */
    public function testDIRequest(): void
    {
        $this->assertInstanceOf(RequestInterface::class, di()->get(RequestInterface::class));
    }

    /**
     * Создание ответа
     */
    public function testDIResponse(): void
    {
        $this->assertInstanceOf(ResponseInterface::class, di()->get(ResponseInterface::class));
    }

    /**
     * Создание сессии
     */
    public function testDISession(): void
    {
        $this->assertInstanceOf(SessionStorageInterface::class, di()->get(SessionStorageInterface::class));
    }

    /**
     * Создание вывода
     */
    public function testDIBuffer(): void
    {
        $this->assertInstanceOf(BufferOutputInterface::class, di()->get(BufferOutputInterface::class));
    }
}

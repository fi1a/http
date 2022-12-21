<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use Fi1a\Http\Request;
use Fi1a\Http\RequestInterface;
use Fi1a\Http\Uri;
use Fi1a\Http\UriInterface;
use PHPUnit\Framework\TestCase;

/**
 * Запрос
 */
class RequestTest extends TestCase
{
    /**
     * Возвращает объет запроса
     */
    private function getRequest(): RequestInterface
    {
        return new Request('/path/to/index.html');
    }

    /**
     * Тестированеи Uri
     */
    public function testUri(): void
    {
        $path = '/new/path/to/index.html';
        $request = $this->getRequest();
        $this->assertInstanceOf(UriInterface::class, $request->getUri());
        $request->setUri(new Uri($path));
        $this->assertInstanceOf(UriInterface::class, $request->getUri());
        $this->assertEquals($path, $request->getUri()->getPath());
    }
}

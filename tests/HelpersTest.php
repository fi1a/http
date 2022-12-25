<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use Fi1a\Http\HttpInterface;
use Fi1a\Http\RedirectResponseInterface;
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

    /**
     * Хелпер возвращающий перенаправление
     */
    public function testRedirect(): void
    {
        $this->assertInstanceOf(RedirectResponseInterface::class, redirect());
    }

    /**
     * Хелпер возвращающий перенаправление
     */
    public function testRedirectLocation(): void
    {
        $redirect = redirect(
            '/redirect/',
            ResponseInterface::HTTP_MOVED_PERMANENTLY,
            ['X-Header' => 'Value']
        );
        $this->assertInstanceOf(RedirectResponseInterface::class, $redirect);
        $this->assertEquals('/redirect/', $redirect->getLocation()->getUri());
        $this->assertEquals(ResponseInterface::HTTP_MOVED_PERMANENTLY, $redirect->getStatus());
        $this->assertTrue($redirect->getHeaders()->hasHeader('X-Header'));
    }

    /**
     * Хелпер возвращающий перенаправление
     */
    public function testRedirectTo(): void
    {
        $redirect = redirect()->to(
            '/redirect/',
            ResponseInterface::HTTP_MOVED_PERMANENTLY,
            ['X-Header' => 'Value']
        );
        $this->assertInstanceOf(RedirectResponseInterface::class, $redirect);
        $this->assertEquals('/redirect/', $redirect->getLocation()->getUri());
        $this->assertEquals(ResponseInterface::HTTP_MOVED_PERMANENTLY, $redirect->getStatus());
        $this->assertTrue($redirect->getHeaders()->hasHeader('X-Header'));
    }
}

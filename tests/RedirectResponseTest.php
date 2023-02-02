<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use Fi1a\Http\HeaderCollection;
use Fi1a\Http\RedirectResponse;
use Fi1a\Http\ResponseInterface;
use Fi1a\Http\Uri;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Перенаправление
 */
class RedirectResponseTest extends TestCase
{
    /**
     * Перенаправление
     */
    public function testRedirect(): void
    {
        $redirect = new RedirectResponse();
        $this->assertNull($redirect->getLocation());
        $redirect = $redirect->to('/redirect/');
        $this->assertEquals('/redirect/', $redirect->getLocation()->uri());
        $this->assertTrue($redirect->headers()->hasHeader('Location'));
    }

    /**
     * Перенаправление
     */
    public function testRedirectUri(): void
    {
        $redirect = new RedirectResponse();
        $this->assertNull($redirect->getLocation());
        $redirect = $redirect->to(new Uri('/redirect/'));
        $this->assertEquals('/redirect/', $redirect->getLocation()->uri());
        $this->assertTrue($redirect->headers()->hasHeader('Location'));
    }

    /**
     * Перенаправление
     */
    public function testRedirectFullUri(): void
    {
        $redirect = new RedirectResponse();
        $redirect = $redirect->to('http://domain.ru/redirect/?foo=bar');
        $this->assertEquals('http://domain.ru/redirect/?foo=bar', $redirect->getLocation()->uri());
        $this->assertTrue($redirect->headers()->hasHeader('Location'));
    }

    /**
     * Исключение при статусе отличном от статуса перенаправления
     */
    public function testRedirectStatusException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new RedirectResponse(ResponseInterface::HTTP_OK);
    }

    /**
     * Исключение при пустом адресе перенаправления
     */
    public function testRedirectEmptyLocationException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $redirect = new RedirectResponse();
        $redirect->to('');
    }

    /**
     * Исключение при ошибке передачи заголовков
     */
    public function testRedirectHeadersException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $redirect = new RedirectResponse();
        $redirect->to('/redirect/', null, 'X-Header');
    }

    /**
     * Установить заголовки
     */
    public function testRedirectSetHeadersCollection(): void
    {
        $redirect = new RedirectResponse();
        $redirect = $redirect->to('/redirect/', null, new HeaderCollection([['X-Header', 'Value']]));
        $this->assertTrue($redirect->headers()->hasHeader('Location'));
        $this->assertTrue($redirect->headers()->hasHeader('X-Header'));
    }

    /**
     * Установить заголовки
     */
    public function testRedirectSetHeaders(): void
    {
        $redirect = new RedirectResponse();
        $redirect = $redirect->to('/redirect/', null, ['X-Header' => 'Value']);
        $this->assertTrue($redirect->headers()->hasHeader('Location'));
        $this->assertTrue($redirect->headers()->hasHeader('X-Header'));
    }

    /**
     * Установить статус
     */
    public function testRedirectSetStatus(): void
    {
        $redirect = new RedirectResponse();
        $redirect = $redirect->to('/redirect/', ResponseInterface::HTTP_MOVED_PERMANENTLY);
        $this->assertEquals(ResponseInterface::HTTP_MOVED_PERMANENTLY, $redirect->status());
        $this->assertTrue($redirect->headers()->hasHeader('Location'));
    }
}

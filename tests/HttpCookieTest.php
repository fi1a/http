<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use Fi1a\Http\HttpCookie;
use PHPUnit\Framework\TestCase;

/**
 * Cookie
 */
class HttpCookieTest extends TestCase
{
    /**
     * Значения по умолчанию
     */
    public function testDefaultValues(): void
    {
        $cookie = new HttpCookie();
        $this->assertNull($cookie->getName());
        $this->assertNull($cookie->getValue());
        $this->assertNull($cookie->getDomain());
        $this->assertEquals('/', $cookie->getPath());
        $this->assertNull($cookie->getMaxAge());
        $this->assertNull($cookie->getExpires());
        $this->assertFalse($cookie->getSecure());
        $this->assertFalse($cookie->getHttpOnly());
        $this->assertTrue($cookie->getNeedSet());
    }

    /**
     * Имя
     */
    public function testName(): void
    {
        $cookie = new HttpCookie(['NeedSet' => false]);
        $this->assertFalse($cookie->getNeedSet());
        $cookie->setName('name');
        $this->assertTrue($cookie->getNeedSet());
    }

    /**
     * Значение
     */
    public function testValue(): void
    {
        $cookie = new HttpCookie(['NeedSet' => false]);
        $this->assertFalse($cookie->getNeedSet());
        $cookie->setValue('value');
        $this->assertTrue($cookie->getNeedSet());
    }

    /**
     * Домен
     */
    public function testDomain(): void
    {
        $cookie = new HttpCookie(['NeedSet' => false]);
        $this->assertFalse($cookie->getNeedSet());
        $cookie->setDomain('domain');
        $this->assertTrue($cookie->getNeedSet());
    }

    /**
     * Путь
     */
    public function testPath(): void
    {
        $cookie = new HttpCookie(['NeedSet' => false]);
        $this->assertFalse($cookie->getNeedSet());
        $cookie->setPath('/path/');
        $this->assertTrue($cookie->getNeedSet());
    }

    /**
     * Время жизни
     */
    public function testMaxAge(): void
    {
        $cookie = new HttpCookie(['NeedSet' => false]);
        $this->assertFalse($cookie->getNeedSet());
        $cookie->setMaxAge(100);
        $this->assertTrue($cookie->getNeedSet());
    }

    /**
     * UNIX timestamp когда кука истечет
     */
    public function testExpires(): void
    {
        $cookie = new HttpCookie(['NeedSet' => false]);
        $this->assertFalse($cookie->getNeedSet());
        $cookie->setExpires(100);
        $this->assertTrue($cookie->getNeedSet());
    }

    /**
     * Флаг secure
     */
    public function testSecure(): void
    {
        $cookie = new HttpCookie(['NeedSet' => false]);
        $this->assertFalse($cookie->getNeedSet());
        $cookie->setSecure(true);
        $this->assertTrue($cookie->getNeedSet());
    }

    /**
     * Флаг HttpOnly
     */
    public function testHttpOnly(): void
    {
        $cookie = new HttpCookie(['NeedSet' => false]);
        $this->assertFalse($cookie->getNeedSet());
        $cookie->setHttpOnly(true);
        $this->assertTrue($cookie->getNeedSet());
    }

    /**
     * Флаг определяющий установку cookie
     */
    public function testNeedSet(): void
    {
        $cookie = new HttpCookie(['NeedSet' => false]);
        $this->assertFalse($cookie->getNeedSet());
        $cookie->setNeedSet(true);
        $this->assertTrue($cookie->getNeedSet());
    }
}

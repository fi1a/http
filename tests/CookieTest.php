<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use Fi1a\Http\Cookie;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * Cookie
 */
class CookieTest extends TestCase
{
    /**
     * Значения по умолчанию
     */
    public function testDefaultValues(): void
    {
        $cookie = new Cookie();
        $this->assertNull($cookie->getName());
        $this->assertNull($cookie->getValue());
        $this->assertNull($cookie->getDomain());
        $this->assertEquals('/', $cookie->getPath());
        $this->assertNull($cookie->getMaxAge());
        $this->assertNull($cookie->getExpires());
        $this->assertFalse($cookie->getSecure());
        $this->assertFalse($cookie->getHttpOnly());
    }

    /**
     * Имя
     */
    public function testName(): void
    {
        $cookie = new Cookie();
        $cookie->setName('name');
        $this->assertEquals('name', $cookie->getName());
    }

    /**
     * Имя (исключение при пустой строке)
     */
    public function testNameException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $cookie = new Cookie();
        $cookie->setName('');
    }

    /**
     * Имя (исключение при недопустимых символах)
     */
    public function testNameSymbolsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $cookie = new Cookie();
        $cookie->setName('\x20');
    }

    /**
     * Значение
     */
    public function testValue(): void
    {
        $cookie = new Cookie();
        $cookie->setValue('value');
        $this->assertEquals('value', $cookie->getValue());
    }

    /**
     * Домен
     */
    public function testDomain(): void
    {
        $cookie = new Cookie();
        $cookie->setValue('domain');
        $this->assertEquals('domain', $cookie->getValue());
    }

    /**
     * Путь
     */
    public function testPath(): void
    {
        $cookie = new Cookie();
        $cookie->setPath('/path/');
        $this->assertEquals('/path/', $cookie->getPath());
    }

    /**
     * Время жизни
     */
    public function testMaxAge(): void
    {
        $cookie = new Cookie();
        $this->assertNull($cookie->getExpires());
        $cookie->setMaxAge(100);
        $this->assertEquals(100, $cookie->getMaxAge());
        $this->assertIsInt($cookie->getExpires());
        $cookie->setMaxAge(null);
        $this->assertNull($cookie->getExpires());
    }

    /**
     * UNIX timestamp когда кука истечет
     */
    public function testExpires(): void
    {
        $cookie = new Cookie();
        $cookie->setExpires(100);
        $this->assertIsInt($cookie->getExpires());
        $cookie->setExpires(null);
        $this->assertNull($cookie->getExpires());
        $cookie->setExpires('13.12.2022 00:00:00');
        $this->assertIsInt($cookie->getExpires());
    }

    /**
     * Кука истекла
     */
    public function testIsExpired(): void
    {
        $cookie = new Cookie();
        $cookie->setExpires(time());
        $this->assertTrue($cookie->isExpired());
    }

    /**
     * Кука истекла
     */
    public function testIsNotExpired(): void
    {
        $cookie = new Cookie();
        $cookie->setExpires(time() + 100);
        $this->assertFalse($cookie->isExpired());
    }

    /**
     * Флаг secure
     */
    public function testSecure(): void
    {
        $cookie = new Cookie();
        $cookie->setSecure(true);
        $this->assertTrue($cookie->getSecure());
    }

    /**
     * Флаг HttpOnly
     */
    public function testHttpOnly(): void
    {
        $cookie = new Cookie();
        $cookie->setHttpOnly(true);
        $this->assertTrue($cookie->getHttpOnly());
    }

    /**
     * Действует только на эту сессию
     */
    public function testSession(): void
    {
        $cookie = new Cookie();
        $cookie->setSession(true);
        $this->assertTrue($cookie->getSession());
    }

    /**
     * Пустое название
     */
    public function testValidateName(): void
    {
        $this->expectException(LogicException::class);
        $cookie = new Cookie();
        $cookie->validate();
    }

    /**
     * Пустое значение
     */
    public function testValidateValue(): void
    {
        $this->expectException(LogicException::class);
        $cookie = new Cookie();
        $cookie->setName('cookie');
        $cookie->validate();
    }

    /**
     * Пустой домен
     */
    public function testValidateDomain(): void
    {
        $this->expectException(LogicException::class);
        $cookie = new Cookie();
        $cookie->setName('cookie');
        $cookie->setValue('value');
        $cookie->validate();
    }
}

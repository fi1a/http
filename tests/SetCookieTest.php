<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use Fi1a\Http\HttpCookie;
use Fi1a\Http\HttpCookieInterface;
use Fi1a\Http\SetCookie;
use Fi1a\Http\SetCookieInterface;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * Устанавливает cookie
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SetCookieTest extends TestCase
{
    /**
     * Возвращает объект установки cookie
     */
    private function getSetCookie(): SetCookieInterface
    {
        return new SetCookie();
    }

    /**
     * Возвращает cookie
     */
    private function getCookie(): HttpCookieInterface
    {
        return new HttpCookie([
            'Domain' => 'domain.ru',
            'Name' => 'cookieName1',
            'Value' => 'value1',
            'Path' => '/',
        ]);
    }

    /**
     * Отправляет cookie
     */
    public function testSet(): void
    {
        $setCookie = $this->getSetCookie();
        $this->assertTrue($setCookie->set($this->getCookie()));
    }

    /**
     * Отправляет cookie (исключение при отсутсвии обязательных полей)
     */
    public function testSetCookieException(): void
    {
        $this->expectException(LogicException::class);
        $setCookie = $this->getSetCookie();
        $cookie = $this->getCookie();
        $cookie->setDomain('');
        $setCookie->set($cookie);
    }

    /**
     * Отправляет cookie без URL-кодирования значения
     */
    public function testSetRaw(): void
    {
        $setCookie = $this->getSetCookie();
        $this->assertTrue($setCookie->setRaw($this->getCookie()));
    }

    /**
     * Отправляет cookie (исключение при отсутсвии обязательных полей)
     */
    public function testSetRawCookieException(): void
    {
        $this->expectException(LogicException::class);
        $setCookie = $this->getSetCookie();
        $cookie = $this->getCookie();
        $cookie->setDomain('');
        $setCookie->setRaw($cookie);
    }
}

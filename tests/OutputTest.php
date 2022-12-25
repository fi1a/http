<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use Fi1a\Http\Output;
use Fi1a\Http\SetCookie;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * Отправляет заголовки и контент
 */
class OutputTest extends TestCase
{
    /**
     * Отправляет заголовки и содержание
     */
    public function testSend(): void
    {
        $setCookie = $this->getMockBuilder(SetCookie::class)
            ->onlyMethods(['doSetCookie'])
            ->getMock();

        $setCookie->method('doSetCookie')->willReturn(true);

        $output = $this->getMockBuilder(Output::class)
            ->setConstructorArgs([$setCookie])
            ->onlyMethods(['header', 'isHeaderSent'])
            ->getMock();

        $output->method('isHeaderSent')->willReturn(false);
        $output->expects($this->atLeastOnce())->method('header');
        $request = request();
        $request->getCookies()->add([
            'Name' => 'CookieName1',
            'Value' => 'Value1',
            'Domain' => 'domain.ru',
        ]);
        $request->getCookies()->add([
            'Name' => 'CookieName2',
            'Value' => 'Value2',
            'Domain' => 'domain.ru',
            'NeedSet' => false,
        ]);
        $response = response();
        $response->withHeader('X-Header', '');
        $output->send($request, $response);
    }

    /**
     * Исключение когда заголовки уже отправлены
     */
    public function testSendHeadersSent(): void
    {
        $this->expectException(LogicException::class);
        $output = new Output(new SetCookie());
        $output->send(request(), response());
    }
}

<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use Fi1a\Http\BufferOutput;
use Fi1a\Http\ContentResponse;
use Fi1a\Http\Request;
use Fi1a\Http\ResponseInterface;
use Fi1a\Http\SetCookie;
use PHPUnit\Framework\TestCase;

/**
 * Буфферизированный вывод
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class BufferOutputTest extends TestCase
{
    /**
     * Буферизация
     */
    public function testBuffer()
    {
        $setCookie = $this->getMockBuilder(SetCookie::class)
            ->onlyMethods(['doSetCookie'])
            ->getMock();

        $setCookie->method('doSetCookie')->willReturn(true);

        $buffer = $this->getMockBuilder(BufferOutput::class)
            ->setConstructorArgs([$setCookie])
            ->onlyMethods(['header', 'isHeaderSent'])
            ->getMock();

        $buffer->method('isHeaderSent')->willReturn(false);
        $buffer->expects($this->atLeastOnce())->method('header');

        $request = new Request('/');
        $response = new ContentResponse(ResponseInterface::HTTP_OK, null, $request);

        $this->assertTrue($buffer->start());
        echo 'buffer';
        $this->assertEquals('buffer', $buffer->end());
        echo 'after';
        $this->assertTrue($buffer->clear());
        $this->assertTrue($buffer->start());
        $buffer->send($request, $response);
        ob_start();
    }

    /**
     * Буферизация
     */
    public function testBufferSendInformational()
    {
        $setCookie = $this->getMockBuilder(SetCookie::class)
            ->onlyMethods(['doSetCookie'])
            ->getMock();

        $setCookie->method('doSetCookie')->willReturn(true);

        $buffer = $this->getMockBuilder(BufferOutput::class)
            ->setConstructorArgs([$setCookie])
            ->onlyMethods(['header', 'isHeaderSent'])
            ->getMock();

        $buffer->method('isHeaderSent')->willReturn(false);
        $buffer->expects($this->atLeastOnce())->method('header');

        $request = new Request('/');
        $response = new ContentResponse(ResponseInterface::HTTP_CONTINUE, null, $request);

        $buffer->send($request, $response);
        ob_start();
    }
}

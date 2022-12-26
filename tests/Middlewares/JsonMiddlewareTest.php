<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http\Middlewares;

use Fi1a\Http\BufferOutput;
use Fi1a\Http\Exception\BadRequestException;
use Fi1a\Http\HeaderCollection;
use Fi1a\Http\HttpCookieCollection;
use Fi1a\Http\JsonResponse;
use Fi1a\Http\Middlewares\JsonMiddleware;
use Fi1a\Http\Middlewares\MiddlewareInterface;
use Fi1a\Http\MimeInterface;
use Fi1a\Http\Request;
use Fi1a\Http\Response;
use Fi1a\Http\ServerCollection;
use Fi1a\Http\SetCookie;
use Fi1a\Http\UploadFileCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Поддержка JSON
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class JsonMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        foreach (http()->getMiddlewares() as $index => $middleware) {
            if ($middleware instanceof JsonMiddleware) {
                http()->getMiddlewares()->delete($index);
            }
        }
    }

    /**
     * @return MockObject|MiddlewareInterface
     */
    private function getMiddleware()
    {
        return $this->getMockBuilder(JsonMiddleware::class)
            ->onlyMethods(['terminate'])
            ->getMock();
    }

    /**
     * JSON-ответ
     */
    public function testResponse(): void
    {
        $buffer = $this->getMockBuilder(BufferOutput::class)
            ->onlyMethods(['send'])
            ->setConstructorArgs([new SetCookie()])
            ->getMock();

        $buffer->method('send');

        buffer($buffer);

        $middleware = $this->getMiddleware();

        $middleware->expects($this->once())->method('terminate');
        http()->withMiddleware($middleware);

        request(new Request('/'));
        response((new JsonResponse())->data(['foo' => 'bar']));
    }

    /**
     * JSON-ответ
     */
    public function testNotResponse(): void
    {
        $middleware = $this->getMiddleware();

        $middleware->expects($this->never())->method('terminate');
        http()->withMiddleware($middleware);

        response(new Response());
    }

    /**
     * JSON-запрос
     */
    public function testRequest(): void
    {
        $middleware = $this->getMiddleware();
        http()->withMiddleware($middleware);
        $request = new Request(
            '/',
            [],
            [],
            [],
            new HttpCookieCollection(),
            new UploadFileCollection(),
            new ServerCollection(),
            new HeaderCollection([
                [
                    'Content-Type',
                    MimeInterface::JSON,
                ],
            ]),
            '{"foo":"bar"}'
        );

        request($request);

        $this->assertEquals(['foo' => 'bar'], $request->body());
    }

    /**
     * JSON-запрос
     */
    public function testRequestJsonError(): void
    {
        $this->expectException(BadRequestException::class);
        $middleware = $this->getMiddleware();
        http()->withMiddleware($middleware);
        $request = new Request(
            '/',
            [],
            [],
            [],
            new HttpCookieCollection(),
            new UploadFileCollection(),
            new ServerCollection(),
            new HeaderCollection([
                [
                    'Content-Type',
                    MimeInterface::JSON,
                ],
            ]),
            '{"foo":"ba}'
        );

        request($request);
    }

    /**
     * JSON-запрос
     */
    public function testRequestJsonEmpty(): void
    {
        $this->expectException(BadRequestException::class);
        $middleware = $this->getMiddleware();
        http()->withMiddleware($middleware);
        $request = new Request(
            '/',
            [],
            [],
            [],
            new HttpCookieCollection(),
            new UploadFileCollection(),
            new ServerCollection(),
            new HeaderCollection([
                [
                    'Content-Type',
                    MimeInterface::JSON,
                ],
            ]),
            ''
        );

        request($request);
    }
}

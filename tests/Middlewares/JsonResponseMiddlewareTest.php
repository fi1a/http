<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http\Middlewares;

use Fi1a\Http\BufferOutput;
use Fi1a\Http\JsonResponse;
use Fi1a\Http\Middlewares\JsonResponseMiddleware;
use Fi1a\Http\Middlewares\MiddlewareInterface;
use Fi1a\Http\Request;
use Fi1a\Http\Response;
use Fi1a\Http\SetCookie;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Поддержка JSON-ответа
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class JsonResponseMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        foreach (http()->getMiddlewares() as $index => $middleware) {
            if ($middleware instanceof JsonResponseMiddleware) {
                http()->getMiddlewares()->delete($index);
            }
        }
    }

    /**
     * @return MockObject|MiddlewareInterface
     */
    private function getMiddleware()
    {
        return $this->getMockBuilder(JsonResponseMiddleware::class)
            ->onlyMethods(['terminate'])
            ->getMock();
    }

    /**
     * Перенаправление
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
     * Перенаправление
     */
    public function testNotResponse(): void
    {
        $middleware = $this->getMiddleware();

        $middleware->expects($this->never())->method('terminate');
        http()->withMiddleware($middleware);

        response(new Response());
    }
}

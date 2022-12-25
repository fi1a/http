<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http\Middlewares;

use Fi1a\Http\Middlewares\MiddlewareInterface;
use Fi1a\Http\Middlewares\RedirectMiddleware;
use Fi1a\Http\RedirectResponse;
use Fi1a\Http\Request;
use Fi1a\Http\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Перенаправления
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class RedirectMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        foreach (http()->getMiddlewares() as $index => $middleware) {
            if ($middleware instanceof RedirectMiddleware) {
                http()->getMiddlewares()->delete($index);
            }
        }
    }

    /**
     * @return MockObject|MiddlewareInterface
     */
    private function getMiddleware()
    {
        return $this->getMockBuilder(RedirectMiddleware::class)
            ->onlyMethods(['terminate'])
            ->getMock();
    }

    /**
     * Перенаправление
     */
    public function testRedirect(): void
    {
        $middleware = $this->getMiddleware();

        $middleware->expects($this->once())->method('terminate');
        http()->withMiddleware($middleware);
        request(new Request('/'));
        response((new RedirectResponse())->to('/redirect/'));
    }

    /**
     * Перенаправление
     */
    public function testNotRedirect(): void
    {
        $middleware = $this->getMiddleware();

        $middleware->expects($this->never())->method('terminate');
        http()->withMiddleware($middleware);

        response(new Response());
    }
}

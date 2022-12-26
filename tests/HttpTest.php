<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use Fi1a\Http\BufferOutput;
use Fi1a\Http\BufferOutputInterface;
use Fi1a\Http\HeaderCollectionInterface;
use Fi1a\Http\Http;
use Fi1a\Http\HttpInterface;
use Fi1a\Http\Request;
use Fi1a\Http\RequestInterface;
use Fi1a\Http\Response;
use Fi1a\Http\ResponseInterface;
use Fi1a\Http\ServerCollectionInterface;
use Fi1a\Http\SessionStorage;
use Fi1a\Http\SessionStorageInterface;
use Fi1a\Http\SetCookie;
use Fi1a\Http\UploadFileInterface;
use Fi1a\Unit\Http\Fixtures\Middleware;
use PHPUnit\Framework\TestCase;

/**
 * Менеджер
 */
class HttpTest extends TestCase
{
    /**
     * Создает запрос
     */
    private function getRequest(): RequestInterface
    {
        return Http::createRequestWithGlobals(
            [
                'foo' => 'bar',
            ],
            [
                'foo' => 'bar',
            ],
            [
                'cookieName1' => 'value1',
                'cookieName2' => 'value2',
            ],
            [
                'file1' => [
                    'name' => 'file.pdf',
                    'type' => 'application/pdf',
                    'tmp_name' => '/tmp/phpzGwvIC',
                    'error' => 0,
                    'size' => 13918,
                ],
                'some' => [
                    'name' => [
                        'file2' => 'file.pdf',
                        'other' => [
                            'path' => [
                                'file3' => [
                                    0 => 'file.pdf',
                                    1 => 'file 2.pdf',
                                ],
                            ],
                        ],
                    ],
                    'type' => [
                        'file2' => 'application/pdf',
                        'other' => [
                            'path' => [
                                'file3' => [
                                    0 => 'application/pdf',
                                    1 => 'application/pdf',
                                ],
                            ],
                        ],
                    ],
                    'tmp_name' => [
                        'file2' => '/tmp/php3LEQ1O',
                        'other' => [
                            'path' => [
                                'file3' => [
                                    0 => '/tmp/phppMpdl1',
                                    1 => '/tmp/phph35AEd',
                                ],
                            ],
                        ],
                    ],
                    'error' => [
                        'file2' => 0,
                        'other' => [
                            'path' => [
                                'file3' => [
                                    0 => 0,
                                    1 => 0,
                                ],
                            ],
                        ],
                    ],
                    'size' => [
                        'file2' => 13918,
                        'other' => [
                            'path' => [
                                'file3' => [
                                    0 => 13918,
                                    1 => 13918,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'HTTP_HOST' => 'domain.ru',
                'HTTP_ACCEPT_ENCODING' => 'gzip, deflate',
                'REQUEST_URI' => '/some/path/',
                'CONTENT_LENGTH' => 100,
            ]
        );
    }

    /**
     * Возвращает класс Http
     */
    private function getHttp(): HttpInterface
    {
        return new Http(
            new Request('/'),
            new SessionStorage(),
            new Response(),
            new BufferOutput(new SetCookie())
        );
    }

    /**
     * Создание экземпляра класса Request из глобальных переменных
     */
    public function testCreateRequestWithGlobals(): void
    {
        $request = $this->getRequest();
        $this->assertCount(1, $request->query());
        $this->assertCount(1, $request->post());
        $this->assertCount(2, $request->files());
        $this->assertCount(2, $request->files()->get('some:other:path:file3'));
        $fileUpload = $request->files()->get('some:other:path:file3:0');
        $this->assertInstanceOf(
            UploadFileInterface::class,
            $fileUpload
        );
        $this->assertEquals('file.pdf', $fileUpload->getName());
        $fileUpload = $request->files()->get('some:other:path:file3:1');
        $this->assertInstanceOf(
            UploadFileInterface::class,
            $fileUpload
        );
        $this->assertEquals('file 2.pdf', $fileUpload->getName());
        $this->assertCount(2, $request->cookies());
        $this->assertInstanceOf(HeaderCollectionInterface::class, $request->headers());
        $this->assertCount(7, $request->headers());
        $this->assertEquals('domain.ru', $request->headers()->getLastHeader('Host')->getValue());
        $this->assertInstanceOf(ServerCollectionInterface::class, $request->server());
        $this->assertCount(17, $request->server());
    }

    /**
     * Экземпляр класса текущего запроса
     */
    public function testRequest(): void
    {
        $request = $this->getRequest();
        $http = $this->getHttp();
        $http->request($request);
        $this->assertInstanceOf(RequestInterface::class, $http->request());
    }

    /**
     * Сессия
     */
    public function testSession(): void
    {
        $http = $this->getHttp();
        $this->assertInstanceOf(SessionStorageInterface::class, $http->session());
    }

    /**
     * Ответ
     */
    public function testResponse(): void
    {
        $http = $this->getHttp();
        $http->response(new Response());
        $this->assertInstanceOf(ResponseInterface::class, $http->response());
    }

    /**
     * Буфер
     */
    public function testBuffer(): void
    {
        $http = $this->getHttp();
        $http->buffer(new BufferOutput(new SetCookie()));
        $this->assertInstanceOf(BufferOutputInterface::class, $http->buffer());
    }

    /**
     * Промежуточное ПО
     */
    public function testMiddlewares(): void
    {
        $http = $this->getHttp();
        $this->assertCount(0, $http->getMiddlewares());
        $http->withMiddleware(new Middleware(), 600);
        $this->assertCount(1, $http->getMiddlewares());
    }
}

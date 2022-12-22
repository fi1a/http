<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use Fi1a\Http\Http;
use Fi1a\Http\RequestInterface;
use Fi1a\Http\UploadFileInterface;
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
            [],
            [],
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
            []
        );
    }

    /**
     * Создание экземпляра класса Request из глобальных переменных
     */
    public function testCreateRequestWithGlobals()
    {
        $request = $this->getRequest();
        $this->assertCount(1, $request->getQuery());
        $this->assertCount(1, $request->getPost());
        $this->assertCount(2, $request->getFiles());
        $this->assertCount(2, $request->getFiles()->get('some:other:path:file3'));
        $fileUpload = $request->getFiles()->get('some:other:path:file3:0');
        $this->assertInstanceOf(
            UploadFileInterface::class,
            $fileUpload
        );
        $this->assertEquals('file.pdf', $fileUpload->getName());
        $fileUpload = $request->getFiles()->get('some:other:path:file3:1');
        $this->assertInstanceOf(
            UploadFileInterface::class,
            $fileUpload
        );
        $this->assertEquals('file 2.pdf', $fileUpload->getName());
    }

    /**
     * Экземпляр класса текущего запроса
     */
    public function testRequest()
    {
        $request = $this->getRequest();
        $http = Http::getInstance();
        $http->setRequest($request);
        $this->assertInstanceOf(RequestInterface::class, $http->getRequest());
    }
}

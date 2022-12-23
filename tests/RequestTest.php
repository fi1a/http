<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use Fi1a\Collection\DataType\PathAccess;
use Fi1a\Collection\DataType\PathAccessInterface;
use Fi1a\Http\Request;
use Fi1a\Http\RequestInterface;
use Fi1a\Http\UploadFile;
use Fi1a\Http\UploadFileCollection;
use Fi1a\Http\UploadFileInterface;
use Fi1a\Http\Uri;
use Fi1a\Http\UriInterface;
use PHPUnit\Framework\TestCase;

/**
 * Запрос
 */
class RequestTest extends TestCase
{
    /**
     * Возвращает объет запроса
     */
    private function getRequest(): RequestInterface
    {
        return new Request(
            '/path/to/index.html',
            [
                'foo' => [
                    'bar' => 'baz',
                ],
            ],
            [
                'foo' => [
                    'bar' => 'baz',
                ],
            ],
            [],
            null,
            null,
            [],
            [],
            fopen(__DIR__ . '/Resources/content1.txt', 'r')
        );
    }

    /**
     * Тестированеи Uri
     */
    public function testUri(): void
    {
        $path = '/new/path/to/index.html';
        $request = $this->getRequest();
        $this->assertInstanceOf(UriInterface::class, $request->getUri());
        $request->setUri(new Uri($path));
        $this->assertInstanceOf(UriInterface::class, $request->getUri());
        $this->assertEquals($path, $request->getUri()->getPath());
    }

    /**
     * POST данные
     */
    public function testPost(): void
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(PathAccessInterface::class, $request->getPost());
        $this->assertCount(1, $request->getPost());
        $this->assertEquals('baz', $request->getPost()->get('foo:bar'));
        $request->setPost([
            'foo' => [
                'bar' => 'qux',
            ],
        ]);
        $this->assertInstanceOf(PathAccessInterface::class, $request->getPost());
        $this->assertCount(1, $request->getPost());
        $this->assertEquals('qux', $request->getPost()->get('foo:bar'));
        $request->setPost(new PathAccess([
            'foo' => [
                'bar' => 'baz',
            ],
        ]));
        $this->assertCount(1, $request->getPost());
        $this->assertEquals('baz', $request->getPost()->get('foo:bar'));
    }

    /**
     * GET значения
     */
    public function testQuery(): void
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(PathAccessInterface::class, $request->getQuery());
        $this->assertCount(1, $request->getQuery());
        $this->assertEquals('baz', $request->getQuery()->get('foo:bar'));
        $request->setQuery([
            'foo' => [
                'bar' => 'qux',
            ],
        ]);
        $this->assertInstanceOf(PathAccessInterface::class, $request->getQuery());
        $this->assertCount(1, $request->getQuery());
        $this->assertEquals('qux', $request->getQuery()->get('foo:bar'));
        $request->setQuery(new PathAccess([
            'foo' => [
                'bar' => 'baz',
            ],
        ]));
        $this->assertCount(1, $request->getQuery());
        $this->assertEquals('baz', $request->getQuery()->get('foo:bar'));
    }

    /**
     * Файлы
     */
    public function testFiles(): void
    {
        $request = $this->getRequest();
        $this->assertCount(0, $request->getFiles());
        $files = new UploadFileCollection();
        $files->set('file1', new UploadFile([
            'error' => 0,
            'name' => 'filename.txt',
            'type' => 'txt',
            'tmp_name' => '/tmp/filename',
            'size' => 100,
        ]));
        $files->set('some:file2', new UploadFile([
            'error' => 0,
            'name' => 'filename2.txt',
            'type' => 'txt',
            'tmp_name' => '/tmp/filename2',
            'size' => 120,
        ]));
        $request->setFiles($files);
        $this->assertCount(2, $request->getFiles());
        $uploadFile = $request->getFiles()->get('some:file2');
        $this->assertInstanceOf(UploadFileInterface::class, $uploadFile);
        $this->assertEquals('filename2.txt', $uploadFile->getName());
    }

    /**
     * Содержание
     */
    public function testContent(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('content1', stream_get_contents($request->getContent()));
        $request->setContent(fopen(__DIR__ . '/Resources/content2.txt', 'r'));
        $this->assertEquals('content2', stream_get_contents($request->getContent()));
    }

    /**
     * Содержание
     */
    public function testContentString(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('content1', stream_get_contents($request->getContent()));
        $request->setContent('content2');
        $this->assertEquals('content2', stream_get_contents($request->getContent()));
    }
}

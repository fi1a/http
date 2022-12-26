<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use Fi1a\Collection\DataType\PathAccess;
use Fi1a\Collection\DataType\PathAccessInterface;
use Fi1a\Http\HeaderCollectionInterface;
use Fi1a\Http\HttpCookieCollectionInterface;
use Fi1a\Http\HttpInterface;
use Fi1a\Http\Request;
use Fi1a\Http\RequestInterface;
use Fi1a\Http\ServerCollection;
use Fi1a\Http\ServerCollectionInterface;
use Fi1a\Http\UploadFile;
use Fi1a\Http\UploadFileCollection;
use Fi1a\Http\UploadFileInterface;
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
            'http://domain.ru:80/path/to/index.html',
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
            null,
            null,
            fopen(__DIR__ . '/Resources/content1.txt', 'r')
        );
    }

    /**
     * POST данные
     */
    public function testPost(): void
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(PathAccessInterface::class, $request->post());
        $this->assertCount(1, $request->post());
        $this->assertEquals('baz', $request->post()->get('foo:bar'));
        $request->setPost([
            'foo' => [
                'bar' => 'qux',
            ],
        ]);
        $this->assertInstanceOf(PathAccessInterface::class, $request->post());
        $this->assertCount(1, $request->post());
        $this->assertEquals('qux', $request->post()->get('foo:bar'));
        $request->setPost(new PathAccess([
            'foo' => [
                'bar' => 'baz',
            ],
        ]));
        $this->assertCount(1, $request->post());
        $this->assertEquals('baz', $request->post()->get('foo:bar'));
    }

    /**
     * GET значения
     */
    public function testQuery(): void
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(PathAccessInterface::class, $request->query());
        $this->assertCount(1, $request->query());
        $this->assertEquals('baz', $request->query()->get('foo:bar'));
        $request->setQuery([
            'foo' => [
                'bar' => 'qux',
            ],
        ]);
        $this->assertInstanceOf(PathAccessInterface::class, $request->query());
        $this->assertCount(1, $request->query());
        $this->assertEquals('qux', $request->query()->get('foo:bar'));
        $request->setQuery(new PathAccess([
            'foo' => [
                'bar' => 'baz',
            ],
        ]));
        $this->assertCount(1, $request->query());
        $this->assertEquals('baz', $request->query()->get('foo:bar'));
    }

    /**
     * Все значения из GET и POST
     */
    public function testAll(): void
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(PathAccessInterface::class, $request->all());
        $this->assertCount(1, $request->all());
        $this->assertEquals('baz', $request->all()->get('foo:bar'));
        $request->setPost([
            'foo' => [
                'bar' => 'qux',
            ],
        ]);
        $this->assertInstanceOf(PathAccessInterface::class, $request->post());
        $this->assertCount(1, $request->all());
        $this->assertEquals('qux', $request->all()->get('foo:bar'));
        $request->setQuery(new PathAccess([
            'qux' => [1, 2, 3],
        ]));
        $this->assertCount(2, $request->all());
        $this->assertEquals([1, 2, 3], $request->all()->get('qux'));
    }

    /**
     * Только переданные ключи из GET и POST
     */
    public function testOnly(): void
    {
        $request = $this->getRequest();
        $request->setPost([
            'foo' => [
                'bar' => 'qux',
            ],
            'baz' => 'quz',
        ]);
        $only = $request->only(['foo']);
        $this->assertInstanceOf(PathAccessInterface::class, $only);
        $this->assertCount(1, $only);
        $this->assertEquals('qux', $only->get('foo:bar'));
    }

    /**
     * Только переданные ключи из GET и POST
     */
    public function testOnlyEmpty(): void
    {
        $request = $this->getRequest();
        $only = $request->only([]);
        $this->assertInstanceOf(PathAccessInterface::class, $only);
        $this->assertCount(0, $only);
    }

    /**
     * Только переданные ключи из GET и POST
     */
    public function testOnlyNotExists(): void
    {
        $request = $this->getRequest();
        $only = $request->only(['not-exists']);
        $this->assertInstanceOf(PathAccessInterface::class, $only);
        $this->assertCount(0, $only);
    }

    /**
     * Только переданные ключи из GET и POST
     */
    public function testOnlyPath(): void
    {
        $request = $this->getRequest();
        $only = $request->only(['foo:bar']);
        $this->assertInstanceOf(PathAccessInterface::class, $only);
        $this->assertCount(1, $only);
        $this->assertEquals('baz', $only->get('foo:bar'));
    }

    /**
     * Файлы
     */
    public function testFiles(): void
    {
        $request = $this->getRequest();
        $this->assertCount(0, $request->files());
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
        $this->assertCount(2, $request->files());
        $uploadFile = $request->files()->get('some:file2');
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

    /**
     * Заголовки
     */
    public function testHeader(): void
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(HeaderCollectionInterface::class, $request->headers());
        $this->assertCount(5, $request->headers());
        $headers = $request->headers();
        $headers[] = [
            'Content-Type',
            'Value1',
        ];
        $headers[] = [
            'User-Agent',
            'Value2',
        ];
        $request->setHeaders($headers);
        $this->assertInstanceOf(HeaderCollectionInterface::class, $request->headers());
        $this->assertCount(7, $request->headers());
    }

    /**
     * Значения SERVER
     */
    public function testServer(): void
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(ServerCollectionInterface::class, $request->server());
        $this->assertCount(5, $request->headers());
        $server = new ServerCollection([
            'HTTP_HOST' => 'domain.ru',
            'HTTP_ACCEPT_ENCODING' => 'gzip, deflate',
        ]);
        $request->setServer($server);
        $this->assertCount(2, $request->server());
        $this->assertEquals('domain.ru', $request->server()['HTTP_HOST']);
    }

    /**
     * Опции
     */
    public function testOptions(): void
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(PathAccessInterface::class, $request->options());
        $this->assertCount(0, $request->options());
        $request->setOptions(['foo' => 'bar']);
        $this->assertCount(1, $request->options());
        $this->assertEquals('bar', $request->options()->get('foo'));
        $request->setOptions(new PathAccess(['baz' => 'qux']));
        $this->assertCount(1, $request->options());
        $this->assertEquals('qux', $request->options()->get('baz'));
    }

    /**
     * Адрес
     */
    public function testPath(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('/path/to/index.html', $request->path());
        $request->setPath('/new/address/');
        $this->assertEquals('/new/address/', $request->path());
    }

    /**
     * Путь без файла
     */
    public function testBasePath(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('/path/to/', $request->basePath());
        $request->setPath('/new/address/');
        $this->assertEquals('/new/address/', $request->path());
    }

    /**
     * Путь без файла со / на конце
     */
    public function testNormalizedBasePath(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('/path/to/', $request->normalizedBasePath());
        $request->setPath('/new/address');
        $this->assertEquals('/new/address/', $request->normalizedBasePath());
    }

    /**
     * Строка запроса
     */
    public function testQueryString(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('foo%5Bbar%5D=baz', $request->queryString());
        $request->setQueryString('qux=quz');
        $this->assertEquals('qux=quz', $request->queryString());
    }

    /**
     * Хост
     */
    public function testHost(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('domain.ru', $request->host());
    }

    /**
     * Хост
     */
    public function testHostEmpty(): void
    {
        $request = new Request('/');
        $request->headers()->withoutHeader('Host');
        $this->assertEquals('', $request->host());
    }

    /**
     * Хост и порт, если он не стандартный
     */
    public function testHttpHttpHost(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('domain.ru', $request->httpHost());
    }

    /**
     * Хост и порт, если он не стандартный
     */
    public function testHttpHttpHostWithPort(): void
    {
        $request = new Request('https://domain.ru:8080/path/');
        $this->assertEquals('domain.ru:8080', $request->httpHost());
    }

    /**
     * Схема, хост и порт
     */
    public function testSchemeAndHttpHost(): void
    {
        $request = new Request('https://domain.ru:8080/path/');
        $this->assertEquals('https://domain.ru:8080', $request->schemeAndHttpHost());
    }

    /**
     * Схема, хост и порт
     */
    public function testSchemeAndHttpHostHttp(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('http://domain.ru', $request->schemeAndHttpHost());
    }

    /**
     * IP адрес клиента
     */
    public function testClientIp(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('127.0.0.1', $request->clientIp());
        $request->server()->set('REMOTE_ADDR', '127.0.0.2');
        $this->assertEquals('127.0.0.2', $request->clientIp());
    }

    /**
     * Использован https
     */
    public function testIsSecure(): void
    {
        $request = $this->getRequest();
        $this->assertFalse($request->isSecure());
        $request->server()->set('HTTPS', 'on');
        $this->assertTrue($request->isSecure());
    }

    /**
     * Возвращает запрошенный файл скрипта
     */
    public function testScriptName(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('', $request->scriptName());
        $request->server()->set('SCRIPT_NAME', __FILE__);
        $this->assertEquals(__FILE__, $request->scriptName());
    }

    /**
     * Возвращает схему запроса
     */
    public function testScheme(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('http', $request->scheme());
        $request->server()->set('HTTPS', 'on');
        $this->assertEquals('https', $request->scheme());
    }

    /**
     * Порт
     */
    public function testPort(): void
    {
        $request = $this->getRequest();
        $this->assertEquals(80, $request->port());
        $request->server()->delete('SERVER_PORT');
        $request->server()->set('HTTPS', 'on');
        $this->assertEquals(443, $request->port());
    }

    /**
     * Возвращает пользователя
     */
    public function testUserEmpty(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('', $request->user());
    }

    /**
     * Возвращает пользователя
     */
    public function testUser(): void
    {
        $request = new Request('https://username:password@domain.ru');
        $this->assertEquals('username', $request->user());
    }

    /**
     * Возвращает пользователя
     */
    public function testPasswordEmpty(): void
    {
        $request = $this->getRequest();
        $this->assertNull($request->password());
    }

    /**
     * Возвращает пользователя
     */
    public function testPassword(): void
    {
        $request = new Request('https://username:password@domain.ru');
        $this->assertEquals('password', $request->password());
    }

    /**
     * Пользователь и пароль
     */
    public function testUserInfoEmpty(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('', $request->userInfo());
    }

    /**
     * Пользователь и пароль
     */
    public function testUserInfo(): void
    {
        $request = new Request('https://username:password@domain.ru');
        $this->assertEquals('username:password', $request->userInfo());
    }

    /**
     * Возвращает путь и строку запроса
     */
    public function testPathAndQuery(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('/path/to/index.html?foo%5Bbar%5D=baz', $request->pathAndQuery());
    }

    /**
     * Возвращает урл с хостом и строку запроса
     */
    public function testUri(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('http://domain.ru/path/to/index.html?foo%5Bbar%5D=baz', $request->uri());
    }

    /**
     * Метод
     */
    public function testMethod(): void
    {
        $request = $this->getRequest();
        $this->assertTrue($request->isMethod(HttpInterface::GET));
        $request->setMethod(HttpInterface::POST);
        $this->assertEquals(HttpInterface::POST, $request->method());
        $this->assertTrue($request->isMethod(HttpInterface::POST));
        $request->setMethod(HttpInterface::PUT);
        $this->assertTrue($request->isMethod('put'));
    }

    /**
     * Возвращает тип содержания
     */
    public function testContentType(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('', $request->contentType());
        $request->headers()->add([
            'Content-Type',
            'application/json',
        ]);
        $this->assertEquals('application/json', $request->contentType());
    }

    /**
     * Без кеша
     */
    public function testIsNoCache(): void
    {
        $request = $this->getRequest();
        $this->assertFalse($request->isNoCache());
        $request->headers()->add([
            'Pragma',
            'no-cache',
        ]);
        $this->assertTrue($request->isNoCache());
    }

    /**
     * Возвращает true если запрос XMLHttpRequest
     */
    public function testIsXmlHttpRequest(): void
    {
        $request = $this->getRequest();
        $this->assertFalse($request->isXmlHttpRequest());
        $request->headers()->add([
            'X-Requested-With',
            'XMLHttpRequest',
        ]);
        $this->assertTrue($request->isXmlHttpRequest());
    }

    /**
     * ETags
     */
    public function testETags(): void
    {
        $request = $this->getRequest();
        $this->assertEquals([], $request->eTags());
        $request->headers()->add([
            'If-None-Match',
            '1,2,3',
        ]);
        $this->assertEquals(['1', '2', '3',], $request->eTags());
        $request = $this->getRequest();
        $request->headers()->add([
            'If-None-Match',
            '',
        ]);
        $this->assertEquals([], $request->eTags());
    }

    /**
     * Возвращает путь до выполняемого скрипта
     */
    public function testScript(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('', $request->script());
        $request->server()->set('SCRIPT_FILENAME', __FILE__);
        $this->assertEquals(__FILE__, $request->script());
    }

    /**
     * Возвращает cookie
     */
    public function testCookie(): void
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(HttpCookieCollectionInterface::class, $request->cookies());
    }
}

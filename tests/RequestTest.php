<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use Fi1a\Collection\DataType\PathAccess;
use Fi1a\Collection\DataType\PathAccessInterface;
use Fi1a\Http\HeaderCollection;
use Fi1a\Http\HeaderCollectionInterface;
use Fi1a\Http\HttpCookieCollectionInterface;
use Fi1a\Http\HttpInterface;
use Fi1a\Http\MimeInterface;
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
                'qux' => 'quz',
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
        $this->assertCount(2, $request->post());
        $this->assertEquals('baz', $request->post()->get('foo:bar'));
    }

    /**
     * POST данные
     */
    public function testPostPathAccess(): void
    {
        $request = new Request(
            'http://domain.ru:80/path/to/index.html',
            [
            ],
            new PathAccess(
                [
                    'foo' => [
                        'bar' => 'baz',
                    ],
                    'qux' => 'quz',
                ]
            )
        );
        $this->assertInstanceOf(PathAccessInterface::class, $request->post());
        $this->assertCount(2, $request->post());
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
    }

    /**
     * GET значения
     */
    public function testQueryPathAccess(): void
    {
        $request = new Request(
            'http://domain.ru:80/path/to/index.html',
            new PathAccess([
                'foo' => [
                    'bar' => 'baz',
                ],
            ])
        );

        $this->assertInstanceOf(PathAccessInterface::class, $request->query());
        $this->assertCount(1, $request->query());
        $this->assertEquals('baz', $request->query()->get('foo:bar'));
    }

    /**
     * Все значения из GET, POST, FILES, BODY
     */
    public function testAll(): void
    {
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
        $request = new Request(
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
                'qux' => 'quz',
            ],
            [],
            null,
            $files,
            null,
            new HeaderCollection([
                [
                    'Content-Type',
                    MimeInterface::JSON,
                ],
            ]),
            '{"json":"value"}'
        );
        $request = request($request);
        $this->assertInstanceOf(PathAccessInterface::class, $request->all());
        $this->assertCount(5, $request->all());
        $this->assertEquals('baz', $request->all()->get('foo:bar'));
        $this->assertInstanceOf(UploadFileInterface::class, $request->all()->get('some:file2'));
        $this->assertEquals('value', $request->all()->get('json'));
    }

    /**
     * Все значения из GET, POST, FILES, BODY
     */
    public function testAllBodyArray(): void
    {
        $request = $this->getRequest();
        $request = $request->setBody(['json' => 'value']);
        $this->assertInstanceOf(PathAccessInterface::class, $request->all());
        $this->assertCount(3, $request->all());
        $this->assertEquals('baz', $request->all()->get('foo:bar'));
        $this->assertEquals('value', $request->all()->get('json'));
    }

    /**
     * Только переданные ключи из GET и POST
     */
    public function testOnly(): void
    {
        $request = $this->getRequest();
        $only = $request->only(['foo']);
        $this->assertInstanceOf(PathAccessInterface::class, $only);
        $this->assertCount(1, $only);
        $this->assertEquals('baz', $only->get('foo:bar'));
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
        $request = new Request(
            'http://domain.ru:80/path/to/index.html',
            [],
            [],
            [],
            null,
            $files
        );
        $this->assertCount(2, $request->files());
        $uploadFile = $request->files()->get('some:file2');
        $this->assertInstanceOf(UploadFileInterface::class, $uploadFile);
        $this->assertEquals('filename2.txt', $uploadFile->getName());
    }

    /**
     * Содержание
     */
    public function testRawBody(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('content1', stream_get_contents($request->rawBody()));
        $request = $request->setRawBody(fopen(__DIR__ . '/Resources/content2.txt', 'r'));
        $this->assertEquals('content2', stream_get_contents($request->rawBody()));
    }

    /**
     * Содержание
     */
    public function testRawBodyString(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('content1', stream_get_contents($request->rawBody()));
        $request = $request->setRawBody('content2');
        $this->assertEquals('content2', stream_get_contents($request->rawBody()));
    }

    /**
     * Содержание
     */
    public function testBody(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('content1', $request->body());
    }

    /**
     * Заголовки
     */
    public function testHeader(): void
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(HeaderCollectionInterface::class, $request->headers());
        $this->assertCount(5, $request->headers());
    }

    /**
     * Значения SERVER
     */
    public function testServer(): void
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(ServerCollectionInterface::class, $request->server());
        $this->assertCount(14, $request->server());
        $server = new ServerCollection([
            'HTTP_HOST' => 'domain.ru',
            'HTTP_ACCEPT_ENCODING' => 'gzip, deflate',
        ]);
        $request = new Request(
            'http://domain.ru:80/path/to/index.html',
            [],
            [],
            [],
            null,
            null,
            $server
        );
        $this->assertCount(15, $request->server());
        $this->assertEquals('domain.ru:80', $request->server()->get('HTTP_HOST'));
    }

    /**
 * Опции
 */
    public function testOptionsEmpty(): void
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(PathAccessInterface::class, $request->options());
        $this->assertCount(0, $request->options());
    }

    /**
     * Опции
     */
    public function testOptions(): void
    {
        $request = new Request(
            'http://domain.ru:80/path/to/index.html',
            [],
            [],
            ['foo' => 'bar']
        );
        $this->assertCount(1, $request->options());
        $this->assertEquals('bar', $request->options()->get('foo'));
    }

    /**
     * Опции
     */
    public function testOptionsPathAccess(): void
    {
        $request = new Request(
            'http://domain.ru:80/path/to/index.html',
            [],
            [],
            new PathAccess(['baz' => 'qux'])
        );
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
    }

    /**
     * Адрес
     */
    public function testSetPath(): void
    {
        $request = new Request('/new/address/');
        $this->assertEquals('/new/address/', $request->path());
    }

    /**
     * Путь без файла
     */
    public function testBasePath(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('/path/to/', $request->basePath());
    }

    /**
     * Путь без файла
     */
    public function testSetBasePath(): void
    {
        $request = new Request('/new/address/');
        $this->assertEquals('/new/address/', $request->path());
    }

    /**
     * Путь без файла со / на конце
     */
    public function testNormalizedBasePath(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('/path/to/', $request->normalizedBasePath());
    }

    /**
     * Путь без файла со / на конце
     */
    public function testSetNormalizedBasePath(): void
    {
        $request = new Request('/new/address');
        $this->assertEquals('/new/address/', $request->normalizedBasePath());
    }

    /**
     * Строка запроса
     */
    public function testQueryString(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('foo%5Bbar%5D=baz', $request->queryString());
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
        $server = new ServerCollection([
            'REMOTE_ADDR' => '127.0.0.2',
        ]);
        $request = new Request(
            'http://domain.ru:80/path/to/index.html',
            [],
            [],
            [],
            null,
            null,
            $server
        );
        $this->assertEquals('127.0.0.2', $request->clientIp());
    }

    /**
     * Использован https
     */
    public function testIsSecure(): void
    {
        $request = $this->getRequest();
        $this->assertFalse($request->isSecure());
        $server = new ServerCollection([
            'HTTPS' => 'on',
        ]);
        $request = new Request(
            'http://domain.ru:80/path/to/index.html',
            [],
            [],
            [],
            null,
            null,
            $server
        );
        $this->assertTrue($request->isSecure());
    }

    /**
     * Возвращает запрошенный файл скрипта
     */
    public function testScriptName(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('', $request->scriptName());
        $server = new ServerCollection([
            'SCRIPT_NAME' => __FILE__,
        ]);
        $request = new Request(
            'http://domain.ru:80/path/to/index.html',
            [],
            [],
            [],
            null,
            null,
            $server
        );
        $this->assertEquals(__FILE__, $request->scriptName());
    }

    /**
     * Возвращает схему запроса
     */
    public function testScheme(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('http', $request->scheme());
        $server = new ServerCollection([
            'HTTPS' => 'on',
        ]);
        $request = new Request(
            'http://domain.ru:80/path/to/index.html',
            [],
            [],
            [],
            null,
            null,
            $server
        );
        $this->assertEquals('https', $request->scheme());
    }

    /**
     * Порт
     */
    public function testPort(): void
    {
        $request = $this->getRequest();
        $this->assertEquals(80, $request->port());
        $server = new ServerCollection([
            'HTTPS' => 'on',
            'SERVER_PORT' => 443,
        ]);
        $request = new Request(
            'https://domain.ru/path/to/index.html',
            [],
            [],
            [],
            null,
            null,
            $server
        );
        $this->assertEquals(443, $request->port());
        $server = new ServerCollection([
            'HTTPS' => 'on',
        ]);
        $request = new Request(
            '/path/to/index.html',
            [],
            [],
            [],
            null,
            null,
            $server
        );
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
        $this->assertTrue($request->isMethod(HttpInterface::GET));
        $this->assertTrue($request->isMethod('get'));
    }

    /**
     * Возвращает тип содержания
     */
    public function testContentType(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('', $request->contentType());
        $request = new Request(
            'http://domain.ru:80/path/to/index.html',
            [],
            [],
            [],
            null,
            null,
            null,
            new HeaderCollection([
                [
                    'Content-Type',
                    'application/json',
                ],
            ])
        );
        $this->assertEquals('application/json', $request->contentType());
    }

    /**
     * Без кеша
     */
    public function testIsNoCache(): void
    {
        $request = $this->getRequest();
        $this->assertFalse($request->isNoCache());
        $request = new Request(
            'http://domain.ru:80/path/to/index.html',
            [],
            [],
            [],
            null,
            null,
            null,
            new HeaderCollection([
                [
                    'Pragma',
                    'no-cache',
                ],
            ])
        );
        $this->assertTrue($request->isNoCache());
    }

    /**
     * Возвращает true если запрос XMLHttpRequest
     */
    public function testIsXmlHttpRequest(): void
    {
        $request = $this->getRequest();
        $this->assertFalse($request->isXmlHttpRequest());
        $request = new Request(
            'http://domain.ru:80/path/to/index.html',
            [],
            [],
            [],
            null,
            null,
            null,
            new HeaderCollection([
                [
                    'X-Requested-With',
                    'XMLHttpRequest',
                ],
            ])
        );
        $this->assertTrue($request->isXmlHttpRequest());
    }

    /**
     * ETags
     */
    public function testETags(): void
    {
        $request = $this->getRequest();
        $this->assertEquals([], $request->eTags());
        $request = new Request(
            'http://domain.ru:80/path/to/index.html',
            [],
            [],
            [],
            null,
            null,
            null,
            new HeaderCollection([
                [
                    'If-None-Match',
                    '1,2,3',
                ],
            ])
        );
        $this->assertEquals(['1', '2', '3',], $request->eTags());
        $request = new Request(
            'http://domain.ru:80/path/to/index.html',
            [],
            [],
            [],
            null,
            null,
            null,
            new HeaderCollection([
                [
                    'If-None-Match',
                    '',
                ],
            ])
        );
        $this->assertEquals([], $request->eTags());
    }

    /**
     * Возвращает путь до выполняемого скрипта
     */
    public function testScript(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('', $request->script());
        $server = new ServerCollection([
            'SCRIPT_FILENAME' => __FILE__,
        ]);
        $request = new Request(
            'http://domain.ru:80/path/to/index.html',
            [],
            [],
            [],
            null,
            null,
            $server
        );
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

<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use Fi1a\Collection\DataType\PathAccess;
use Fi1a\Collection\DataType\PathAccessInterface;
use Fi1a\Http\HeaderCollectionInterface;
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

    /**
     * Заголовки
     */
    public function testHeader(): void
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(HeaderCollectionInterface::class, $request->getHeaders());
        $this->assertCount(5, $request->getHeaders());
        $headers = $request->getHeaders();
        $headers[] = [
            'Content-Type',
            'Value1',
        ];
        $headers[] = [
            'User-Agent',
            'Value2',
        ];
        $request->setHeaders($headers);
        $this->assertInstanceOf(HeaderCollectionInterface::class, $request->getHeaders());
        $this->assertCount(7, $request->getHeaders());
    }

    /**
     * Значения SERVER
     */
    public function testServer(): void
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(ServerCollectionInterface::class, $request->getServer());
        $this->assertCount(5, $request->getHeaders());
        $server = new ServerCollection([
            'HTTP_HOST' => 'domain.ru',
            'HTTP_ACCEPT_ENCODING' => 'gzip, deflate',
        ]);
        $request->setServer($server);
        $this->assertCount(2, $request->getServer());
        $this->assertEquals('domain.ru', $request->getServer()['HTTP_HOST']);
    }

    /**
     * Опции
     */
    public function testOptions(): void
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(PathAccessInterface::class, $request->getOptions());
        $this->assertCount(0, $request->getOptions());
        $request->setOptions(['foo' => 'bar']);
        $this->assertCount(1, $request->getOptions());
        $this->assertEquals('bar', $request->getOptions()->get('foo'));
        $request->setOptions(new PathAccess(['baz' => 'qux']));
        $this->assertCount(1, $request->getOptions());
        $this->assertEquals('qux', $request->getOptions()->get('baz'));
    }

    /**
     * Адрес
     */
    public function testPath(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('/path/to/index.html', $request->getPath());
        $request->setPath('/new/address/');
        $this->assertEquals('/new/address/', $request->getPath());
    }

    /**
     * Путь без файла
     */
    public function testBasePath(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('/path/to/', $request->getBasePath());
        $request->setPath('/new/address/');
        $this->assertEquals('/new/address/', $request->getPath());
    }

    /**
     * Путь без файла со / на конце
     */
    public function testNormalizedBasePath(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('/path/to/', $request->getNormalizedBasePath());
        $request->setPath('/new/address');
        $this->assertEquals('/new/address/', $request->getNormalizedBasePath());
    }

    /**
     * Строка запроса
     */
    public function testQueryString(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('foo%5Bbar%5D=baz', $request->getQueryString());
        $request->setQueryString('qux=quz');
        $this->assertEquals('qux=quz', $request->getQueryString());
    }

    /**
     * Хост
     */
    public function testGetHost(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('domain.ru', $request->getHost());
    }

    /**
     * Хост
     */
    public function testGetHostEmpty(): void
    {
        $request = new Request('/');
        $request->getHeaders()->withoutHeader('Host');
        $this->assertEquals('', $request->getHost());
    }

    /**
     * Хост и порт, если он не стандартный
     */
    public function testHttpGetHttpHost(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('domain.ru', $request->getHttpHost());
    }

    /**
     * Хост и порт, если он не стандартный
     */
    public function testHttpGetHttpHostWithPort(): void
    {
        $request = new Request('https://domain.ru:8080/path/');
        $this->assertEquals('domain.ru:8080', $request->getHttpHost());
    }

    /**
     * Схема, хост и порт
     */
    public function testGetSchemeAndHttpHost(): void
    {
        $request = new Request('https://domain.ru:8080/path/');
        $this->assertEquals('https://domain.ru:8080', $request->getSchemeAndHttpHost());
    }

    /**
     * Схема, хост и порт
     */
    public function testGetSchemeAndHttpHostHttp(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('http://domain.ru', $request->getSchemeAndHttpHost());
    }

    /**
     * IP адрес клиента
     */
    public function testGetClientIp(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('127.0.0.1', $request->getClientIp());
        $request->getServer()->set('REMOTE_ADDR', '127.0.0.2');
        $this->assertEquals('127.0.0.2', $request->getClientIp());
    }

    /**
     * Использован https
     */
    public function testIsSecure(): void
    {
        $request = $this->getRequest();
        $this->assertFalse($request->isSecure());
        $request->getServer()->set('HTTPS', 'on');
        $this->assertTrue($request->isSecure());
    }

    /**
     * Возвращает запрошенный файл скрипта
     */
    public function testGetScriptName(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('', $request->getScriptName());
        $request->getServer()->set('SCRIPT_NAME', __FILE__);
        $this->assertEquals(__FILE__, $request->getScriptName());
    }

    /**
     * Возвращает схему запроса
     */
    public function testGetScheme(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('http', $request->getScheme());
        $request->getServer()->set('HTTPS', 'on');
        $this->assertEquals('https', $request->getScheme());
    }

    /**
     * Порт
     */
    public function testGetPort(): void
    {
        $request = $this->getRequest();
        $this->assertEquals(80, $request->getPort());
        $request->getServer()->delete('SERVER_PORT');
        $request->getServer()->set('HTTPS', 'on');
        $this->assertEquals(443, $request->getPort());
    }

    /**
     * Возвращает пользователя
     */
    public function testGetUserEmpty(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('', $request->getUser());
    }

    /**
     * Возвращает пользователя
     */
    public function testGetUser(): void
    {
        $request = new Request('https://username:password@domain.ru');
        $this->assertEquals('username', $request->getUser());
    }

    /**
     * Возвращает пользователя
     */
    public function testGetPasswordEmpty(): void
    {
        $request = $this->getRequest();
        $this->assertNull($request->getPassword());
    }

    /**
     * Возвращает пользователя
     */
    public function testGetPassword(): void
    {
        $request = new Request('https://username:password@domain.ru');
        $this->assertEquals('password', $request->getPassword());
    }

    /**
     * Пользователь и пароль
     */
    public function testGetUserInfoEmpty(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('', $request->getUserInfo());
    }

    /**
     * Пользователь и пароль
     */
    public function testGetUserInfo(): void
    {
        $request = new Request('https://username:password@domain.ru');
        $this->assertEquals('username:password', $request->getUserInfo());
    }

    /**
     * Возвращает путь и строку запроса
     */
    public function testGetPathAndQuery(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('/path/to/index.html?foo%5Bbar%5D=baz', $request->getPathAndQuery());
    }

    /**
     * Возвращает урл с хостом и строку запроса
     */
    public function testGetUri(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('http://domain.ru/path/to/index.html?foo%5Bbar%5D=baz', $request->getUri());
    }

    /**
     * Метод
     */
    public function testMethod(): void
    {
        $request = $this->getRequest();
        $this->assertTrue($request->isMethod(HttpInterface::GET));
        $request->setMethod(HttpInterface::POST);
        $this->assertEquals(HttpInterface::POST, $request->getMethod());
        $this->assertTrue($request->isMethod(HttpInterface::POST));
        $request->setMethod(HttpInterface::PUT);
        $this->assertTrue($request->isMethod('put'));
    }

    /**
     * Возвращает тип содержания
     */
    public function testGetContentType(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('', $request->getContentType());
        $request->getHeaders()->add([
            'Content-Type',
            'application/json',
        ]);
        $this->assertEquals('application/json', $request->getContentType());
    }

    /**
     * Без кеша
     */
    public function testIsNoCache(): void
    {
        $request = $this->getRequest();
        $this->assertFalse($request->isNoCache());
        $request->getHeaders()->add([
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
        $request->getHeaders()->add([
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
        $this->assertEquals([], $request->getETags());
        $request->getHeaders()->add([
            'If-None-Match',
            '1,2,3',
        ]);
        $this->assertEquals(['1', '2', '3',], $request->getETags());
        $request = $this->getRequest();
        $request->getHeaders()->add([
            'If-None-Match',
            '',
        ]);
        $this->assertEquals([], $request->getETags());
    }

    /**
     * Возвращает путь до выполняемого скрипта
     */
    public function testGetScript(): void
    {
        $request = $this->getRequest();
        $this->assertEquals('', $request->getScript());
        $request->getServer()->set('SCRIPT_FILENAME', __FILE__);
        $this->assertEquals(__FILE__, $request->getScript());
    }
}

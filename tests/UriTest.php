<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use Fi1a\Collection\DataType\PathAccess;
use Fi1a\Collection\DataType\PathAccessInterface;
use Fi1a\Http\Uri;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * URI
 */
class UriTest extends TestCase
{
    /**
     * Пустой объект
     */
    public function testEmptyUri(): void
    {
        $uri = new Uri();
        $this->assertEquals('https', $uri->scheme());
        $this->assertEquals('', $uri->host());
    }

    /**
     * Схема
     */
    public function testEmptyScheme(): void
    {
        $uri = new Uri('host.ru');
        $this->assertEquals('https', $uri->scheme());
    }

    /**
     * Схема
     */
    public function testHttpsScheme(): void
    {
        $uri = new Uri('https://host.ru/');
        $this->assertEquals('https', $uri->scheme());
    }

    /**
     * Схема
     */
    public function testHttpScheme(): void
    {
        $uri = new Uri('http://host.ru/');
        $this->assertEquals('http', $uri->scheme());
    }

    /**
     * Схема
     */
    public function testSchemeInUppercase(): void
    {
        $uri = new Uri('HTTP://host.ru/');
        $this->assertEquals('http', $uri->scheme());
    }

    /**
     * Использован https
     */
    public function testIsSecure(): void
    {
        $uri = new Uri('https://host.ru/');
        $this->assertTrue($uri->isSecure());
    }

    /**
     * Использован https
     */
    public function testIsSecureHttp(): void
    {
        $uri = new Uri('http://host.ru/');
        $this->assertFalse($uri->isSecure());
    }

    /**
     * Использован https
     */
    public function testIsSecureUppercase(): void
    {
        $uri = new Uri('HTTPS://host.ru/');
        $this->assertTrue($uri->isSecure());
    }

    /**
     * Схема
     */
    public function testOnlyScheme(): void
    {
        $uri = new Uri('http');
        $this->assertEquals('https', $uri->scheme());
    }

    /**
     * Схема
     */
    public function testUnknownScheme(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Uri('unknown://host.ru/');
    }

    /**
     * Компонент информации о пользователе URI
     */
    public function testUserInfo(): void
    {
        $uri = new Uri('https://username:password@host.ru/');
        $this->assertEquals('username:password', $uri->userInfo());
    }

    /**
     * Компонент информации о пользователе URI
     */
    public function testUser(): void
    {
        $uri = new Uri('https://username:password@host.ru/');
        $this->assertEquals('username', $uri->user());
    }

    /**
     * Компонент информации о пользователе URI
     */
    public function testPassword(): void
    {
        $uri = new Uri('https://username:password@host.ru/');
        $this->assertEquals('password', $uri->password());
    }

    /**
     * Компонент информации о пользователе URI
     */
    public function testUserInfoWithoutPassword(): void
    {
        $uri = new Uri('https://username@host.ru/');
        $this->assertEquals('username', $uri->userInfo());
    }

    /**
     * Компонент информации о пользователе URI
     */
    public function testUserInfoEmptyPassword(): void
    {
        $uri = new Uri('https://username:@host.ru/');
        $this->assertEquals('username:', $uri->userInfo());
    }

    /**
     * Компонент информации о пользователе URI
     */
    public function testUserInfoEmpty(): void
    {
        $uri = new Uri('https://host.ru/');
        $this->assertEquals('', $uri->userInfo());
    }

    /**
     * Хост
     */
    public function testHost(): void
    {
        $uri = new Uri('https://host.ru/');
        $this->assertEquals('host.ru', $uri->host());
    }

    /**
     * Хост
     */
    public function testHostEmptyScheme(): void
    {
        $uri = new Uri('host.ru');
        $this->assertEquals('', $uri->host());
    }

    /**
     * Хост
     */
    public function testHostEmpty(): void
    {
        $uri = new Uri('https');
        $this->assertEquals('', $uri->host());
    }

    /**
     * Порт
     */
    public function testPort(): void
    {
        $uri = new Uri('https://host.ru:8080');
        $this->assertEquals(8080, $uri->port());
    }

    /**
     * Порт
     */
    public function testEmptyPort(): void
    {
        $uri = new Uri('https://host.ru');
        $this->assertNull($uri->port());
    }

    /**
     * Порт
     */
    public function testEmptyPortSyntaxError(): void
    {
        $uri = new Uri('https://host.ru:');
        $this->assertNull($uri->port());
    }

    /**
     * Часть пути URI
     */
    public function testPath(): void
    {
        $uri = new Uri('https://host.ru/some/path/');
        $this->assertEquals('/some/path/', $uri->path());
    }

    /**
     * Часть пути URI
     */
    public function testPathEmpty(): void
    {
        $uri = new Uri('https://host.ru');
        $this->assertEquals('/', $uri->path());
    }

    /**
     * Урл без файла
     */
    public function testBasePathWithFile(): void
    {
        $uri = new Uri('https://host.ru/some/path/index.php');
        $this->assertEquals('/some/path/', $uri->basePath());
    }

    /**
     * Урл без файла
     */
    public function testBasePathWithFolder(): void
    {
        $uri = new Uri('https://host.ru/some/path/');
        $this->assertEquals('/some/path/', $uri->basePath());
        $uri = new Uri('https://host.ru/some/path');
        $this->assertEquals('/some/path', $uri->basePath());
    }

    /**
     * Урл без файла
     */
    public function testBasePathRoot(): void
    {
        $uri = new Uri('https://host.ru/');
        $this->assertEquals('/', $uri->basePath());
    }

    /**
     * Урл без файла
     */
    public function testBasePathEmpty(): void
    {
        $uri = new Uri('https://host.ru');
        $this->assertEquals('/', $uri->basePath());
    }

    /**
     * Урл без файла со / на конце
     */
    public function testNormalizedBasePathWithFolder(): void
    {
        $uri = new Uri('https://host.ru/some/path/');
        $this->assertEquals('/some/path/', $uri->normalizedBasePath());
        $uri = new Uri('https://host.ru/some/path');
        $this->assertEquals('/some/path/', $uri->normalizedBasePath());
    }

    /**
     * Урл без файла со / на конце
     */
    public function testNormalizedBasePathEmpty(): void
    {
        $uri = new Uri('https://host.ru');
        $this->assertEquals('/', $uri->normalizedBasePath());
    }

    /**
     * Строка запроса в URI
     */
    public function testQuery(): void
    {
        $uri = new Uri('https://host.ru/some/path/?foo=bar&baz[]=qux&baz[]=quz');
        $this->assertEquals('foo=bar&baz%5B0%5D=qux&baz%5B1%5D=quz', $uri->query());
    }

    /**
     * Строка запроса в URI (кодирование)
     */
    public function testWithQueryParamsEncode(): void
    {
        $uri = new Uri('https://host.ru/some/path/?foo=Один два&baz[]=три&baz[]=четыре');
        $this->assertEquals(
            'foo=%D0%9E%D0%B4%D0%B8%D0%BD+%D0%B4%D0%B2%D0%B0&baz%5B0%5D=%D1%82%D1%80%D0%B8&baz'
            . '%5B1%5D=%D1%87%D0%B5%D1%82%D1%8B%D1%80%D0%B5',
            $uri->query()
        );
    }

    /**
     * Строка запроса в URI
     */
    public function testQueryEmptySyntaxError(): void
    {
        $uri = new Uri('https://host.ru/some/path/?');
        $this->assertEquals('', $uri->query());
    }

    /**
     * Строка запроса в URI
     */
    public function testQueryEmpty(): void
    {
        $uri = new Uri('https://host.ru/some/path/');
        $this->assertEquals('', $uri->query());
    }

    /**
     * Массив запроса в URI
     */
    public function testQueryParamsPathAccess(): void
    {
        $uri = new Uri('https://host.ru/some/path/?foo=bar&baz[]=qux&baz[]=quz');
        $this->assertInstanceOf(PathAccessInterface::class, $uri->queryParams());
        $this->assertCount(2, $uri->queryParams());
        $this->assertEquals(['qux', 'quz'], $uri->queryParams()->get('baz'));
        $uri = $uri->withQueryParams(new PathAccess(['foo' => 'bar']));
        $this->assertCount(1, $uri->queryParams());
        $this->assertEquals('bar', $uri->queryParams()->get('foo'));
    }

    /**
     * Массив запроса в URI
     */
    public function testQueryParams(): void
    {
        $uri = new Uri('https://host.ru/some/path/?foo=bar&baz[]=qux&baz[]=quz');
        $this->assertEquals(
            ['foo' => 'bar', 'baz' => ['qux', 'quz']],
            $uri->queryParams()->getArrayCopy()
        );
    }

    /**
     * Массив запроса в URI
     */
    public function testQueryParamsEmpty(): void
    {
        $uri = new Uri('https://host.ru/some/path/');
        $this->assertEquals([], $uri->queryParams()->getArrayCopy());
    }

    /**
     * Массив запроса в URI
     */
    public function testWithQueryParams(): void
    {
        $queryParams = ['foo' => 'bar', 'baz' => ['qux', 'quz']];
        $uri = new Uri('https://host.ru/some/path/');
        $uri = $uri->withQueryParams($queryParams);
        $this->assertEquals($queryParams, $uri->queryParams()->getArrayCopy());
        $this->assertEquals('foo=bar&baz%5B0%5D=qux&baz%5B1%5D=quz', $uri->query());
    }

    /**
     * Фрагмент URI
     */
    public function testFragment(): void
    {
        $uri = new Uri('https://host.ru/some/path/#fragment');
        $this->assertEquals('fragment', $uri->fragment());
    }

    /**
     * Фрагмент URI
     */
    public function testFragmentEmptyErrorSyntax(): void
    {
        $uri = new Uri('https://host.ru/some/path/#');
        $this->assertEquals('', $uri->fragment());
    }

    /**
     * Фрагмент URI
     */
    public function testFragmentEmpty(): void
    {
        $uri = new Uri('https://host.ru/some/path/');
        $this->assertEquals('', $uri->fragment());
    }

    /**
     * URL
     */
    public function testUrl(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080/some/path/?foo=bar#fragment');
        $this->assertEquals('https://username:password@host.ru:8080/some/path/', $uri->url());
    }

    /**
     * URL
     */
    public function testUrlWithoutPort(): void
    {
        $uri = new Uri('https://username:password@host.ru/some/path/');
        $this->assertEquals('https://username:password@host.ru/some/path/', $uri->url());
    }

    /**
     * URL
     */
    public function testUrlWithoutUserInfo(): void
    {
        $uri = new Uri('https://host.ru/some/path/');
        $this->assertEquals('https://host.ru/some/path/', $uri->url());
    }

    /**
     * URL
     */
    public function testUrlWithoutPath(): void
    {
        $uri = new Uri('https://host.ru');
        $this->assertEquals('https://host.ru/', $uri->url());
    }

    /**
     * URL
     */
    public function testUrlEmpty(): void
    {
        $uri = new Uri('https');
        $this->assertEquals('https', $uri->url());
    }

    /**
     * URI
     */
    public function testUri(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080/some/path/?foo=bar#fragment');
        $this->assertEquals(
            'https://username:password@host.ru:8080/some/path/?foo=bar#fragment',
            $uri->uri()
        );
    }

    /**
     * URI
     */
    public function testUriEmpty(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080/some/path/?');
        $this->assertEquals('https://username:password@host.ru:8080/some/path/', $uri->uri());
    }

    /**
     * URI
     */
    public function testUriPathOtherEmpty(): void
    {
        $uri = new Uri('https');
        $this->assertEquals('https', $uri->uri());
    }

    /**
     * URI
     */
    public function testUriRelativePath(): void
    {
        $uri = new Uri('relative/path/');
        $this->assertEquals('relative/path/', $uri->uri());
    }

    /**
     * URI
     */
    public function testUriEmptyUrl(): void
    {
        $uri = new Uri('');
        $this->assertEquals('/', $uri->uri());
    }

    /**
     * Возвращает путь и строку запроса
     */
    public function testPathAndQuery(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080/some/path/?foo=bar#fragment');
        $this->assertEquals(
            '/some/path/?foo=bar',
            $uri->pathAndQuery()
        );
    }

    /**
     * Возвращает путь и строку запроса
     */
    public function testPathAndQueryEmpty(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080/some/path/?');
        $this->assertEquals('/some/path/', $uri->pathAndQuery());
    }

    /**
     * Возвращает путь и строку запроса
     */
    public function testPathAndQueryEmptyUrl(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080');
        $this->assertEquals('/', $uri->pathAndQuery());
    }

    /**
     * Компонент полномочий URI
     */
    public function testAuthority(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080/some/path/');
        $this->assertEquals('username:password@host.ru:8080', $uri->authority());
    }

    /**
     * Компонент полномочий URI
     */
    public function testAuthorityHost(): void
    {
        $uri = new Uri('https://host.ru/some/path/');
        $this->assertEquals('host.ru', $uri->authority());
    }

    /**
     * Компонент полномочий URI
     */
    public function testAuthorityEmpty(): void
    {
        $uri = new Uri('https');
        $this->assertEquals('', $uri->authority());
    }

    /**
     * Возвращает URI с маской на данных авторизации
     */
    public function testMaskedUri(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080/some/path/?foo=bar#fragment');
        $this->assertEquals(
            'https://######:######@host.ru:8080/some/path/?foo=bar#fragment',
            $uri->maskedUri()
        );
    }

    /**
     * Возвращает URI с маской на данных авторизации
     */
    public function testMaskedUriEmpty(): void
    {
        $uri = new Uri('https');
        $this->assertEquals(
            '',
            $uri->maskedUri()
        );
    }

    /**
     * Заменить адрес
     */
    public function testReplace(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080/some/path/?foo=bar#fragment');
        $uri = $uri->replace('/new/path');
        $this->assertEquals(
            'https://username:password@host.ru:8080/new/path',
            $uri->uri()
        );
    }

    /**
     * Заменить адрес
     */
    public function testReplaceWithQuery(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080/some/path/?foo=bar#fragment');
        $uri = $uri->replace('/new/path?baz=qux');
        $this->assertEquals(
            'https://username:password@host.ru:8080/new/path?baz=qux',
            $uri->uri()
        );
    }

    /**
     * Заменить адрес
     */
    public function testReplaceWithFragment(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080/some/path/?foo=bar#fragment');
        $uri = $uri->replace('/new/path?baz=qux#new-fragment');
        $this->assertEquals(
            'https://username:password@host.ru:8080/new/path?baz=qux#new-fragment',
            $uri->uri()
        );
    }

    /**
     * Заменить адрес
     */
    public function testReplaceFull(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080/some/path/?foo=bar#fragment');
        $uri = $uri->replace('https://new-username:new-password@new-host.ru:8181/new/path/?baz=qux#new-fragment');
        $this->assertEquals(
            'https://new-username:new-password@new-host.ru:8181/new/path/?baz=qux#new-fragment',
            $uri->uri()
        );
    }

    /**
     * Форматирование в Uri
     */
    public function testFormatInUri(): void
    {
        $uri = new Uri('https://host.ru/some/path/{{id}}#fragment', ['id' => 100,]);
        $this->assertEquals(
            'https://host.ru/some/path/100#fragment',
            $uri->uri()
        );
    }

    /**
     * Форматирование в Uri
     */
    public function testFormatInReplaceUri(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080/some/path/?foo=bar#fragment');
        $uri = $uri->replace(
            'https://new-username:new-password@new-host.ru:8181/new/path/?baz={{baz}}#new-fragment',
            ['baz' => 'qux']
        );
        $this->assertEquals(
            'https://new-username:new-password@new-host.ru:8181/new/path/?baz=qux#new-fragment',
            $uri->uri()
        );
    }

    /**
     * Тестирование клонирования
     */
    public function testClone(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080/some/path/?foo=bar#fragment');
        $clone = clone $uri;

        $this->assertTrue($clone !== $uri);
        $this->assertTrue($uri->queryParams() !== $clone->queryParams());
    }
}

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
        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('', $uri->getHost());
    }

    /**
     * Схема
     */
    public function testGetEmptyScheme(): void
    {
        $uri = new Uri('host.ru');
        $this->assertEquals('https', $uri->getScheme());
    }

    /**
     * Схема
     */
    public function testGetHttpsScheme(): void
    {
        $uri = new Uri('https://host.ru/');
        $this->assertEquals('https', $uri->getScheme());
    }

    /**
     * Схема
     */
    public function testGetHttpScheme(): void
    {
        $uri = new Uri('http://host.ru/');
        $this->assertEquals('http', $uri->getScheme());
    }

    /**
     * Схема
     */
    public function testGetSchemeInUppercase(): void
    {
        $uri = new Uri('HTTP://host.ru/');
        $this->assertEquals('http', $uri->getScheme());
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
    public function testGetOnlyScheme(): void
    {
        $uri = new Uri('http');
        $this->assertEquals('https', $uri->getScheme());
    }

    /**
     * Схема
     */
    public function testGetUnknownScheme(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Uri('unknown://host.ru/');
    }

    /**
     * Компонент информации о пользователе URI
     */
    public function testGetUserInfo(): void
    {
        $uri = new Uri('https://username:password@host.ru/');
        $this->assertEquals('username:password', $uri->getUserInfo());
    }

    /**
     * Компонент информации о пользователе URI
     */
    public function testGetUser(): void
    {
        $uri = new Uri('https://username:password@host.ru/');
        $this->assertEquals('username', $uri->getUser());
    }

    /**
     * Компонент информации о пользователе URI
     */
    public function testGetPassword(): void
    {
        $uri = new Uri('https://username:password@host.ru/');
        $this->assertEquals('password', $uri->getPassword());
    }

    /**
     * Компонент информации о пользователе URI
     */
    public function testGetUserInfoWithoutPassword(): void
    {
        $uri = new Uri('https://username@host.ru/');
        $this->assertEquals('username', $uri->getUserInfo());
    }

    /**
     * Компонент информации о пользователе URI
     */
    public function testGetUserInfoEmptyPassword(): void
    {
        $uri = new Uri('https://username:@host.ru/');
        $this->assertEquals('username:', $uri->getUserInfo());
    }

    /**
     * Компонент информации о пользователе URI
     */
    public function testGetUserInfoEmpty(): void
    {
        $uri = new Uri('https://host.ru/');
        $this->assertEquals('', $uri->getUserInfo());
    }

    /**
     * Хост
     */
    public function testGetHost(): void
    {
        $uri = new Uri('https://host.ru/');
        $this->assertEquals('host.ru', $uri->getHost());
    }

    /**
     * Хост
     */
    public function testGetHostEmptyScheme(): void
    {
        $uri = new Uri('host.ru');
        $this->assertEquals('', $uri->getHost());
    }

    /**
     * Хост
     */
    public function testGetHostEmpty(): void
    {
        $uri = new Uri('https');
        $this->assertEquals('', $uri->getHost());
    }

    /**
     * Порт
     */
    public function testGetPort(): void
    {
        $uri = new Uri('https://host.ru:8080');
        $this->assertEquals(8080, $uri->getPort());
    }

    /**
     * Порт
     */
    public function testGetEmptyPort(): void
    {
        $uri = new Uri('https://host.ru');
        $this->assertNull($uri->getPort());
    }

    /**
     * Порт
     */
    public function testGetEmptyPortSyntaxError(): void
    {
        $uri = new Uri('https://host.ru:');
        $this->assertNull($uri->getPort());
    }

    /**
     * Часть пути URI
     */
    public function testGetPath(): void
    {
        $uri = new Uri('https://host.ru/some/path/');
        $this->assertEquals('/some/path/', $uri->getPath());
    }

    /**
     * Часть пути URI
     */
    public function testGetPathEmpty(): void
    {
        $uri = new Uri('https://host.ru');
        $this->assertEquals('/', $uri->getPath());
    }

    /**
     * Урл без файла
     */
    public function testGetBasePathWithFile(): void
    {
        $uri = new Uri('https://host.ru/some/path/index.php');
        $this->assertEquals('/some/path/', $uri->getBasePath());
    }

    /**
     * Урл без файла
     */
    public function testGetBasePathWithFolder(): void
    {
        $uri = new Uri('https://host.ru/some/path/');
        $this->assertEquals('/some/path/', $uri->getBasePath());
        $uri = new Uri('https://host.ru/some/path');
        $this->assertEquals('/some/path', $uri->getBasePath());
    }

    /**
     * Урл без файла
     */
    public function testGetBasePathRoot(): void
    {
        $uri = new Uri('https://host.ru/');
        $this->assertEquals('/', $uri->getBasePath());
    }

    /**
     * Урл без файла
     */
    public function testGetBasePathEmpty(): void
    {
        $uri = new Uri('https://host.ru');
        $this->assertEquals('/', $uri->getBasePath());
    }

    /**
     * Урл без файла со / на конце
     */
    public function testGetNormalizedBasePathWithFolder(): void
    {
        $uri = new Uri('https://host.ru/some/path/');
        $this->assertEquals('/some/path/', $uri->getNormalizedBasePath());
        $uri = new Uri('https://host.ru/some/path');
        $this->assertEquals('/some/path/', $uri->getNormalizedBasePath());
    }

    /**
     * Урл без файла со / на конце
     */
    public function testGetNormalizedBasePathEmpty(): void
    {
        $uri = new Uri('https://host.ru');
        $this->assertEquals('/', $uri->getNormalizedBasePath());
    }

    /**
     * Строка запроса в URI
     */
    public function testGetQuery(): void
    {
        $uri = new Uri('https://host.ru/some/path/?foo=bar&baz[]=qux&baz[]=quz');
        $this->assertEquals('foo=bar&baz%5B0%5D=qux&baz%5B1%5D=quz', $uri->getQuery());
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
            $uri->getQuery()
        );
    }

    /**
     * Строка запроса в URI
     */
    public function testGetQueryEmptySyntaxError(): void
    {
        $uri = new Uri('https://host.ru/some/path/?');
        $this->assertEquals('', $uri->getQuery());
    }

    /**
     * Строка запроса в URI
     */
    public function testGetQueryEmpty(): void
    {
        $uri = new Uri('https://host.ru/some/path/');
        $this->assertEquals('', $uri->getQuery());
    }

    /**
     * Массив запроса в URI
     */
    public function testGetQueryParamsPathAccess(): void
    {
        $uri = new Uri('https://host.ru/some/path/?foo=bar&baz[]=qux&baz[]=quz');
        $this->assertInstanceOf(PathAccessInterface::class, $uri->getQueryParams());
        $this->assertCount(2, $uri->getQueryParams());
        $this->assertEquals(['qux', 'quz'], $uri->getQueryParams()->get('baz'));
        $uri = $uri->withQueryParams(new PathAccess(['foo' => 'bar']));
        $this->assertCount(1, $uri->getQueryParams());
        $this->assertEquals('bar', $uri->getQueryParams()->get('foo'));
    }

    /**
     * Массив запроса в URI
     */
    public function testGetQueryParams(): void
    {
        $uri = new Uri('https://host.ru/some/path/?foo=bar&baz[]=qux&baz[]=quz');
        $this->assertEquals(
            ['foo' => 'bar', 'baz' => ['qux', 'quz']],
            $uri->getQueryParams()->getArrayCopy()
        );
    }

    /**
     * Массив запроса в URI
     */
    public function testGetQueryParamsEmpty(): void
    {
        $uri = new Uri('https://host.ru/some/path/');
        $this->assertEquals([], $uri->getQueryParams()->getArrayCopy());
    }

    /**
     * Массив запроса в URI
     */
    public function testWithQueryParams(): void
    {
        $queryParams = ['foo' => 'bar', 'baz' => ['qux', 'quz']];
        $uri = new Uri('https://host.ru/some/path/');
        $uri = $uri->withQueryParams($queryParams);
        $this->assertEquals($queryParams, $uri->getQueryParams()->getArrayCopy());
        $this->assertEquals('foo=bar&baz%5B0%5D=qux&baz%5B1%5D=quz', $uri->getQuery());
    }

    /**
     * Фрагмент URI
     */
    public function testGetFragment(): void
    {
        $uri = new Uri('https://host.ru/some/path/#fragment');
        $this->assertEquals('fragment', $uri->getFragment());
    }

    /**
     * Фрагмент URI
     */
    public function testGetFragmentEmptyErrorSyntax(): void
    {
        $uri = new Uri('https://host.ru/some/path/#');
        $this->assertEquals('', $uri->getFragment());
    }

    /**
     * Фрагмент URI
     */
    public function testGetFragmentEmpty(): void
    {
        $uri = new Uri('https://host.ru/some/path/');
        $this->assertEquals('', $uri->getFragment());
    }

    /**
     * URL
     */
    public function testGetUrl(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080/some/path/?foo=bar#fragment');
        $this->assertEquals('https://username:password@host.ru:8080/some/path/', $uri->getUrl());
    }

    /**
     * URL
     */
    public function testGetUrlWithoutPort(): void
    {
        $uri = new Uri('https://username:password@host.ru/some/path/');
        $this->assertEquals('https://username:password@host.ru/some/path/', $uri->getUrl());
    }

    /**
     * URL
     */
    public function testGetUrlWithoutUserInfo(): void
    {
        $uri = new Uri('https://host.ru/some/path/');
        $this->assertEquals('https://host.ru/some/path/', $uri->getUrl());
    }

    /**
     * URL
     */
    public function testGetUrlWithoutPath(): void
    {
        $uri = new Uri('https://host.ru');
        $this->assertEquals('https://host.ru/', $uri->getUrl());
    }

    /**
     * URL
     */
    public function testGetUrlEmpty(): void
    {
        $uri = new Uri('https');
        $this->assertEquals('https', $uri->getUrl());
    }

    /**
     * URI
     */
    public function testGetUri(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080/some/path/?foo=bar#fragment');
        $this->assertEquals(
            'https://username:password@host.ru:8080/some/path/?foo=bar#fragment',
            $uri->getUri()
        );
    }

    /**
     * URI
     */
    public function testGetUriEmpty(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080/some/path/?');
        $this->assertEquals('https://username:password@host.ru:8080/some/path/', $uri->getUri());
    }

    /**
     * URI
     */
    public function testGetUriPathOtherEmpty(): void
    {
        $uri = new Uri('https');
        $this->assertEquals('https', $uri->getUri());
    }

    /**
     * URI
     */
    public function testGetUriRelativePath(): void
    {
        $uri = new Uri('relative/path/');
        $this->assertEquals('relative/path/', $uri->getUri());
    }

    /**
     * URI
     */
    public function testGetUriEmptyUrl(): void
    {
        $uri = new Uri('');
        $this->assertEquals('/', $uri->getUri());
    }

    /**
     * Возвращает путь и строку запроса
     */
    public function testGetPathAndQuery(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080/some/path/?foo=bar#fragment');
        $this->assertEquals(
            '/some/path/?foo=bar',
            $uri->getPathAndQuery()
        );
    }

    /**
     * Возвращает путь и строку запроса
     */
    public function testGetPathAndQueryEmpty(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080/some/path/?');
        $this->assertEquals('/some/path/', $uri->getPathAndQuery());
    }

    /**
     * Возвращает путь и строку запроса
     */
    public function testGetPathAndQueryEmptyUrl(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080');
        $this->assertEquals('/', $uri->getPathAndQuery());
    }

    /**
     * Компонент полномочий URI
     */
    public function testGetAuthority(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080/some/path/');
        $this->assertEquals('username:password@host.ru:8080', $uri->getAuthority());
    }

    /**
     * Компонент полномочий URI
     */
    public function testGetAuthorityHost(): void
    {
        $uri = new Uri('https://host.ru/some/path/');
        $this->assertEquals('host.ru', $uri->getAuthority());
    }

    /**
     * Компонент полномочий URI
     */
    public function testGetAuthorityEmpty(): void
    {
        $uri = new Uri('https');
        $this->assertEquals('', $uri->getAuthority());
    }

    /**
     * Возвращает URI с маской на данных авторизации
     */
    public function testGetMaskedUri(): void
    {
        $uri = new Uri('https://username:password@host.ru:8080/some/path/?foo=bar#fragment');
        $this->assertEquals(
            'https://######:######@host.ru:8080/some/path/?foo=bar#fragment',
            $uri->getMaskedUri()
        );
    }

    /**
     * Возвращает URI с маской на данных авторизации
     */
    public function testGetMaskedUriEmpty(): void
    {
        $uri = new Uri('https');
        $this->assertEquals(
            '',
            $uri->getMaskedUri()
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
            $uri->getUri()
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
            $uri->getUri()
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
            $uri->getUri()
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
            $uri->getUri()
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
            $uri->getUri()
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
            $uri->getUri()
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
        $this->assertTrue($uri->getQueryParams() !== $clone->getQueryParams());
    }
}

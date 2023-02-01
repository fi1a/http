<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use DateTime;
use Fi1a\Http\ContentResponse;
use Fi1a\Http\HeaderCollection;
use Fi1a\Http\HeaderCollectionInterface;
use Fi1a\Http\Response;
use Fi1a\Http\ResponseInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Ответ
 */
class ResponseTest extends TestCase
{
    /**
     * Возвращает объект класса ответа
     */
    private function getResponse(): ResponseInterface
    {
        return new Response();
    }

    /**
     * Тестированеи кода статуса
     */
    public function testStatus(): void
    {
        $response = $this->getResponse();
        $this->assertEquals(ResponseInterface::HTTP_OK, $response->status());
        $this->assertEquals('OK', $response->reasonPhrase());
        $response = $response->withStatus(ResponseInterface::HTTP_ACCEPTED);
        $this->assertEquals(ResponseInterface::HTTP_ACCEPTED, $response->status());
        $this->assertEquals('Accepted', $response->reasonPhrase());
    }

    /**
     * Тестированеи кода статуса и текста
     */
    public function testReasonPhrase(): void
    {
        $response = $this->getResponse();
        $response = $response->withStatus(ResponseInterface::HTTP_ACCEPTED, 'New Accepted');
        $this->assertEquals(ResponseInterface::HTTP_ACCEPTED, $response->status());
        $this->assertEquals('New Accepted', $response->reasonPhrase());
    }

    /**
     * Тестированеи кода статуса и текста
     */
    public function testReasonPhraseNull(): void
    {
        $response = $this->getResponse();
        $response = $response->withStatus(105);
        $this->assertNull($response->reasonPhrase());
    }

    /**
     * Тестированеи кода статуса
     */
    public function testStatusException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $response = $this->getResponse();
        $response->withStatus(10);
    }

    /**
     * Тестированеи кода статуса
     */
    public function testStatusExceptionMax(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $response = $this->getResponse();
        $response->withStatus(601);
    }

    /**
     * Заголоки
     */
    public function testGetHeaders(): void
    {
        $response = $this->getResponse();
        $this->assertInstanceOf(HeaderCollectionInterface::class, $response->headers());
        $this->assertCount(2, $response->headers());
    }

    /**
     * Заголоки
     */
    public function testWithHeaders(): void
    {
        $response = $this->getResponse();
        $headers = new HeaderCollection();
        $headers[] = [
            'X-Header',
            'Value1',
        ];
        $response = $response->withHeaders($headers);
        $this->assertInstanceOf(HeaderCollectionInterface::class, $response->headers());
        $this->assertCount(1, $response->headers());
    }

    /**
     * Заголоки
     */
    public function testWithHeader(): void
    {
        $response = $this->getResponse();
        $this->assertInstanceOf(HeaderCollectionInterface::class, $response->headers());
        $this->assertCount(2, $response->headers());
        $response = $response->withHeader('X-Header', 'Value1');
        $this->assertInstanceOf(HeaderCollectionInterface::class, $response->headers());
        $this->assertCount(3, $response->headers());
    }

    /**
     * Заголоки
     */
    public function testWithHeaderException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $response = $this->getResponse();
        $response->withHeader('', '');
    }

    /**
     * Заголоки
     */
    public function testWithoutHeader(): void
    {
        $response = $this->getResponse();
        $response = $response->withHeader('X-Header', 'Value1');
        $response = $response->withHeader('X-Header', 'Value2');
        $response = $response->withHeader('X-Header-Other', 'Value3');
        $this->assertCount(5, $response->headers());
        $response = $response->withoutHeader('X-Header');
        $this->assertCount(3, $response->headers());
    }

    /**
     * Наличие заголовка
     */
    public function testHasHeader(): void
    {
        $response = $this->getResponse();
        $response = $response->withHeader('X-Header', 'Value1');
        $this->assertTrue($response->hasHeader('X-Header'));
        $this->assertFalse($response->hasHeader('X-Not-Exists'));
    }

    /**
     * Версия протокола
     */
    public function testHttpVersion(): void
    {
        $response = $this->getResponse();
        $this->assertEquals('1.1', $response->httpVersion());
        $response = $response->withHttpVersion('1.0');
        $this->assertEquals('1.0', $response->httpVersion());
    }

    /**
     * Если true, то ответ пустой
     */
    public function testIsEmpty(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isEmpty());
        $response = $response->withStatus(ResponseInterface::HTTP_NO_CONTENT);
        $this->assertTrue($response->isEmpty());
        $response = $response->withStatus(ResponseInterface::HTTP_NOT_MODIFIED);
        $this->assertTrue($response->isEmpty());
    }

    /**
     * Если true, то ответ информационный
     */
    public function testIsInformational(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isInformational());
        $response = $response->withStatus(ResponseInterface::HTTP_CONTINUE);
        $this->assertTrue($response->isInformational());
    }

    /**
     * Если true, то ответ успешный
     */
    public function testIsSuccessful(): void
    {
        $response = $this->getResponse();
        $this->assertTrue($response->isSuccessful());
        $response = $response->withStatus(ResponseInterface::HTTP_CONTINUE);
        $this->assertFalse($response->isSuccessful());
    }

    /**
     * Если true, то клиентская ошибка
     */
    public function testIsClientError(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isClientError());
        $response = $response->withStatus(ResponseInterface::HTTP_BAD_REQUEST);
        $this->assertTrue($response->isClientError());
    }

    /**
     * Если true, то серверная ошибка
     */
    public function testIsServerError(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isServerError());
        $response = $response->withStatus(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertTrue($response->isServerError());
    }

    /**
     * Если true, то ответ 200 OK
     */
    public function testIsOk(): void
    {
        $response = $this->getResponse();
        $this->assertTrue($response->isOk());
        $response = $response->withStatus(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertFalse($response->isOk());
    }

    /**
     * Если true, то 403 Forbidden
     */
    public function testIsForbidden(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isForbidden());
        $response = $response->withStatus(ResponseInterface::HTTP_FORBIDDEN);
        $this->assertTrue($response->isForbidden());
    }

    /**
     * Если true, то 404 Not found
     */
    public function testIsNotFound(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isNotFound());
        $response = $response->withStatus(ResponseInterface::HTTP_NOT_FOUND);
        $this->assertTrue($response->isNotFound());
    }

    /**
     * Если true, то перенаправление
     */
    public function testIsRedirection(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isRedirection());
        $response = $response->withStatus(ResponseInterface::HTTP_PERMANENTLY_REDIRECT);
        $response = $response->withHeader('Location', '/redirect/');
        $this->assertTrue($response->isRedirection());
        $this->assertFalse($response->isRedirection('/path/'));
        $this->assertTrue($response->isRedirection('/redirect/'));
    }

    /**
     * Кодировка
     */
    public function testCharset(): void
    {
        $response = $this->getResponse();
        $this->assertEquals('utf-8', $response->charset());
        $response = $response->withCharset('windows-1251');
        $this->assertEquals('windows-1251', $response->charset());
    }

    /**
     * Подготовка информационного запроса
     */
    public function testPrepareInformational(): void
    {
        $response = $this->getResponse();
        $response = $response->withHeader('Content-Type', 'application/json');
        $response = $response->withHeader('Content-Length', '100');
        $this->assertTrue($response->hasHeader('Content-Type'));
        $this->assertTrue($response->hasHeader('Content-Length'));
        $response = $response->withStatus(ResponseInterface::HTTP_CONTINUE);
        $this->assertFalse($response->hasHeader('Content-Type'));
        $this->assertFalse($response->hasHeader('Content-Length'));
    }

    /**
     * Подготовка запроса
     */
    public function testPrepareDefault(): void
    {
        $response = $this->getResponse();
        $response = $response->withHeader('Transfer-Encoding', 'gzip');
        $response = $response->withHeader('Content-Length', '100');
        $response = $response->withStatus(ResponseInterface::HTTP_OK);
        $this->assertFalse($response->hasHeader('Content-Length'));
    }

    /**
     * Дата
     */
    public function testDate(): void
    {
        $response = $this->getResponse();
        $this->assertInstanceOf(DateTime::class, $response->date());
        $response = $response->withDate(DateTime::createFromFormat('d.m.Y H:i:s', '23.12.2022 09:55:10'));
        $this->assertInstanceOf(DateTime::class, $response->date());
    }

    /**
     * Время последнего изменения
     */
    public function testLastModified(): void
    {
        $response = $this->getResponse();
        $this->assertNull($response->lastModified());
        $response = $response->withLastModified(new DateTime());
        $this->assertInstanceOf(DateTime::class, $response->lastModified());
        $response = $response->withLastModified(null);
        $this->assertNull($response->lastModified());
    }

    /**
     * Содержимое
     */
    public function testContent(): void
    {
        $response = new ContentResponse();
        $this->assertEquals('', $response->getContent());
        $response = $response->setContent('content');
        $this->assertEquals('content', $response->getContent());
    }

    /**
     * Тестирование клонирования
     */
    public function testClone(): void
    {
        $response = $this->getResponse();
        $clone = clone $response;

        $this->assertTrue($clone !== $response);
        $this->assertTrue($response->headers() !== $clone->headers());
        $this->assertTrue($response->cookies() !== $clone->cookies());
    }
}

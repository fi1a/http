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
        $this->assertEquals(ResponseInterface::HTTP_OK, $response->getStatus());
        $this->assertEquals('OK', $response->getReasonPhrase());
        $response = $response->setStatus(ResponseInterface::HTTP_ACCEPTED);
        $this->assertEquals(ResponseInterface::HTTP_ACCEPTED, $response->getStatus());
        $this->assertEquals('Accepted', $response->getReasonPhrase());
    }

    /**
     * Тестированеи кода статуса и текста
     */
    public function testReasonPhrase(): void
    {
        $response = $this->getResponse();
        $response = $response->setStatus(ResponseInterface::HTTP_ACCEPTED, 'New Accepted');
        $this->assertEquals(ResponseInterface::HTTP_ACCEPTED, $response->getStatus());
        $this->assertEquals('New Accepted', $response->getReasonPhrase());
    }

    /**
     * Тестированеи кода статуса и текста
     */
    public function testReasonPhraseNull(): void
    {
        $response = $this->getResponse();
        $response = $response->setStatus(105);
        $this->assertNull($response->getReasonPhrase());
    }

    /**
     * Тестированеи кода статуса
     */
    public function testStatusException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $response = $this->getResponse();
        $response->setStatus(10);
    }

    /**
     * Тестированеи кода статуса
     */
    public function testStatusExceptionMax(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $response = $this->getResponse();
        $response->setStatus(601);
    }

    /**
     * Заголоки
     */
    public function testGetHeaders(): void
    {
        $response = $this->getResponse();
        $this->assertInstanceOf(HeaderCollectionInterface::class, $response->getHeaders());
        $this->assertCount(2, $response->getHeaders());
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
        $this->assertInstanceOf(HeaderCollectionInterface::class, $response->getHeaders());
        $this->assertCount(1, $response->getHeaders());
    }

    /**
     * Заголоки
     */
    public function testWithHeader(): void
    {
        $response = $this->getResponse();
        $this->assertInstanceOf(HeaderCollectionInterface::class, $response->getHeaders());
        $this->assertCount(2, $response->getHeaders());
        $response = $response->withHeader('X-Header', 'Value1');
        $this->assertInstanceOf(HeaderCollectionInterface::class, $response->getHeaders());
        $this->assertCount(3, $response->getHeaders());
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
        $this->assertCount(5, $response->getHeaders());
        $response = $response->withoutHeader('X-Header');
        $this->assertCount(3, $response->getHeaders());
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
        $this->assertEquals('1.1', $response->getHttpVersion());
        $response = $response->setHttpVersion('1.0');
        $this->assertEquals('1.0', $response->getHttpVersion());
    }

    /**
     * Если true, то ответ пустой
     */
    public function testIsEmpty(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isEmpty());
        $response = $response->setStatus(ResponseInterface::HTTP_NO_CONTENT);
        $this->assertTrue($response->isEmpty());
        $response = $response->setStatus(ResponseInterface::HTTP_NOT_MODIFIED);
        $this->assertTrue($response->isEmpty());
    }

    /**
     * Если true, то ответ информационный
     */
    public function testIsInformational(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isInformational());
        $response = $response->setStatus(ResponseInterface::HTTP_CONTINUE);
        $this->assertTrue($response->isInformational());
    }

    /**
     * Если true, то ответ успешный
     */
    public function testIsSuccessful(): void
    {
        $response = $this->getResponse();
        $this->assertTrue($response->isSuccessful());
        $response = $response->setStatus(ResponseInterface::HTTP_CONTINUE);
        $this->assertFalse($response->isSuccessful());
    }

    /**
     * Если true, то клиентская ошибка
     */
    public function testIsClientError(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isClientError());
        $response = $response->setStatus(ResponseInterface::HTTP_BAD_REQUEST);
        $this->assertTrue($response->isClientError());
    }

    /**
     * Если true, то серверная ошибка
     */
    public function testIsServerError(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isServerError());
        $response = $response->setStatus(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertTrue($response->isServerError());
    }

    /**
     * Если true, то ответ 200 OK
     */
    public function testIsOk(): void
    {
        $response = $this->getResponse();
        $this->assertTrue($response->isOk());
        $response = $response->setStatus(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertFalse($response->isOk());
    }

    /**
     * Если true, то 403 Forbidden
     */
    public function testIsForbidden(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isForbidden());
        $response = $response->setStatus(ResponseInterface::HTTP_FORBIDDEN);
        $this->assertTrue($response->isForbidden());
    }

    /**
     * Если true, то 404 Not found
     */
    public function testIsNotFound(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isNotFound());
        $response = $response->setStatus(ResponseInterface::HTTP_NOT_FOUND);
        $this->assertTrue($response->isNotFound());
    }

    /**
     * Если true, то перенаправление
     */
    public function testIsRedirection(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isRedirection());
        $response = $response->setStatus(ResponseInterface::HTTP_PERMANENTLY_REDIRECT);
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
        $this->assertEquals('utf-8', $response->getCharset());
        $response = $response->setCharset('windows-1251');
        $this->assertEquals('windows-1251', $response->getCharset());
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
        $response = $response->setStatus(ResponseInterface::HTTP_CONTINUE);
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
        $response = $response->setStatus(ResponseInterface::HTTP_OK);
        $this->assertFalse($response->hasHeader('Content-Length'));
    }

    /**
     * Дата
     */
    public function testDate(): void
    {
        $response = $this->getResponse();
        $this->assertInstanceOf(DateTime::class, $response->getDate());
        $response = $response->setDate(DateTime::createFromFormat('d.m.Y H:i:s', '23.12.2022 09:55:10'));
        $this->assertInstanceOf(DateTime::class, $response->getDate());
    }

    /**
     * Время последнего изменения
     */
    public function testLastModified(): void
    {
        $response = $this->getResponse();
        $this->assertNull($response->getLastModified());
        $response = $response->setLastModified(new DateTime());
        $this->assertInstanceOf(DateTime::class, $response->getLastModified());
        $response = $response->setLastModified(null);
        $this->assertNull($response->getLastModified());
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
}

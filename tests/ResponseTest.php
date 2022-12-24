<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

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
        $response->setStatus(ResponseInterface::HTTP_ACCEPTED);
        $this->assertEquals(ResponseInterface::HTTP_ACCEPTED, $response->getStatus());
        $this->assertEquals('Accepted', $response->getReasonPhrase());
    }

    /**
     * Тестированеи кода статуса и текста
     */
    public function testReasonPhrase(): void
    {
        $response = $this->getResponse();
        $response->setStatus(ResponseInterface::HTTP_ACCEPTED, 'New Accepted');
        $this->assertEquals(ResponseInterface::HTTP_ACCEPTED, $response->getStatus());
        $this->assertEquals('New Accepted', $response->getReasonPhrase());
    }

    /**
     * Тестированеи кода статуса и текста
     */
    public function testReasonPhraseNull(): void
    {
        $response = $this->getResponse();
        $response->setStatus(105);
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
        $this->assertCount(0, $response->getHeaders());
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
        $response->withHeaders($headers);
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
        $this->assertCount(0, $response->getHeaders());
        $response->withHeader('X-Header', 'Value1');
        $this->assertInstanceOf(HeaderCollectionInterface::class, $response->getHeaders());
        $this->assertCount(1, $response->getHeaders());
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
        $response->withHeader('X-Header', 'Value1');
        $response->withHeader('X-Header', 'Value2');
        $response->withHeader('X-Header-Other', 'Value3');
        $this->assertCount(3, $response->getHeaders());
        $response->withoutHeader('X-Header');
        $this->assertCount(1, $response->getHeaders());
    }

    /**
     * Версия протокола
     */
    public function testHttpVersion(): void
    {
        $response = $this->getResponse();
        $this->assertEquals('1.0', $response->getHttpVersion());
        $response->setHttpVersion('1.1');
        $this->assertEquals('1.1', $response->getHttpVersion());
    }

    /**
     * Если true, то ответ пустой
     */
    public function testIsEmpty(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isEmpty());
        $response->setStatus(ResponseInterface::HTTP_NO_CONTENT);
        $this->assertTrue($response->isEmpty());
        $response->setStatus(ResponseInterface::HTTP_NOT_MODIFIED);
        $this->assertTrue($response->isEmpty());
    }

    /**
     * Если true, то ответ информационный
     */
    public function testIsInformational(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isInformational());
        $response->setStatus(ResponseInterface::HTTP_CONTINUE);
        $this->assertTrue($response->isInformational());
    }

    /**
     * Если true, то ответ успешный
     */
    public function testIsSuccessful(): void
    {
        $response = $this->getResponse();
        $this->assertTrue($response->isSuccessful());
        $response->setStatus(ResponseInterface::HTTP_CONTINUE);
        $this->assertFalse($response->isSuccessful());
    }

    /**
     * Если true, то клиентская ошибка
     */
    public function testIsClientError(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isClientError());
        $response->setStatus(ResponseInterface::HTTP_BAD_REQUEST);
        $this->assertTrue($response->isClientError());
    }

    /**
     * Если true, то серверная ошибка
     */
    public function testIsServerError(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isServerError());
        $response->setStatus(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertTrue($response->isServerError());
    }

    /**
     * Если true, то ответ 200 OK
     */
    public function testIsOk(): void
    {
        $response = $this->getResponse();
        $this->assertTrue($response->isOk());
        $response->setStatus(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertFalse($response->isOk());
    }

    /**
     * Если true, то 403 Forbidden
     */
    public function testIsForbidden(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isForbidden());
        $response->setStatus(ResponseInterface::HTTP_FORBIDDEN);
        $this->assertTrue($response->isForbidden());
    }

    /**
     * Если true, то 404 Not found
     */
    public function testIsNotFound(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isNotFound());
        $response->setStatus(ResponseInterface::HTTP_NOT_FOUND);
        $this->assertTrue($response->isNotFound());
    }

    /**
     * Если true, то перенаправление
     */
    public function testIsRedirection(): void
    {
        $response = $this->getResponse();
        $this->assertFalse($response->isRedirection());
        $response->setStatus(ResponseInterface::HTTP_PERMANENTLY_REDIRECT);
        $response->withHeader('Location', '/redirect/');
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
        $response->setCharset('windows-1251');
        $this->assertEquals('windows-1251', $response->getCharset());
    }
}

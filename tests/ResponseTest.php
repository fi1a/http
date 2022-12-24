<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

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
}

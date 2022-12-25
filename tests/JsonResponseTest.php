<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use Fi1a\Http\JsonResponse;
use Fi1a\Http\JsonResponseInterface;
use Fi1a\Http\ResponseInterface;
use PHPUnit\Framework\TestCase;

use const JSON_HEX_AMP;
use const JSON_HEX_APOS;
use const JSON_HEX_QUOT;
use const JSON_HEX_TAG;

/**
 * JSON ответ
 */
class JsonResponseTest extends TestCase
{
    /**
     * Возвращает объект JSON ответа
     */
    private function getJsonResponse(): JsonResponseInterface
    {
        return new JsonResponse();
    }

    /**
     * Опции используемые для кодирования данных в JSON.
     */
    public function testEncodingOptions(): void
    {
        $response = $this->getJsonResponse();
        $this->assertEquals(
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT,
            $response->getEncodingOptions()
        );
        $response->setEncodingOptions(JSON_HEX_TAG);
        $this->assertEquals(JSON_HEX_TAG, $response->getEncodingOptions());
    }

    /**
     * Кодирование
     */
    public function testSetData(): void
    {
        $response = $this->getJsonResponse();
        $response->data(['foo' => 'bar']);
        $this->assertEquals('{"foo":"bar"}', $response->getContent());
    }

    /**
     * Кодирование
     */
    public function testSetDataWithStatus(): void
    {
        $response = $this->getJsonResponse();
        $response->data(['foo' => 'bar'], ResponseInterface::HTTP_ACCEPTED);
        $this->assertEquals(ResponseInterface::HTTP_ACCEPTED, $response->getStatus());
        $this->assertEquals('{"foo":"bar"}', $response->getContent());
    }

    /**
     * Кодирование
     */
    public function testSetDataException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $response = $this->getJsonResponse();
        $response->data($this);
    }
}

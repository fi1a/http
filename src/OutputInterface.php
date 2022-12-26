<?php

declare(strict_types=1);

namespace Fi1a\Http;

/**
 * Отправляет заголовки
 */
interface OutputInterface
{
    /**
     * Отправляет заголовки
     */
    public function send(RequestInterface $request, ResponseInterface $response): void;

    /**
     * Отправляет заголовки
     */
    public function sendHeaders(RequestInterface $request, ResponseInterface $response): void;

    /**
     * Отправляет содержимое
     */
    public function sendContent(ResponseInterface $response): void;
}

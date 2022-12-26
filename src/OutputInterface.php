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
    public function send(ResponseInterface $response): void;

    /**
     * Отправляет заголовки
     */
    public function sendHeaders(ResponseInterface $response): void;

    /**
     * Отправляет содержимое
     */
    public function sendContent(ResponseInterface $response): void;
}

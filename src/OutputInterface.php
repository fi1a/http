<?php

declare(strict_types=1);

namespace Fi1a\Http;

/**
 * Отправляет заголовки и контент
 */
interface OutputInterface
{
    /**
     * Отправляет заголовки и содержание
     */
    public function send(RequestInterface $request, ResponseInterface $response): void;
}

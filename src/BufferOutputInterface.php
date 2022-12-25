<?php

declare(strict_types=1);

namespace Fi1a\Http;

/**
 * Буфферизированный вывод
 */
interface BufferOutputInterface extends OutputInterface
{
    /**
     * Начало буферизации
     */
    public function start(): bool;

    /**
     * Возвращает буферизированный вывод
     */
    public function get(): string;

    /**
     * Очищает буфер
     */
    public function clear(): bool;

    /**
     * Завершает буферизацию
     */
    public function end(): string;
}

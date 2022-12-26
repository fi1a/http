<?php

declare(strict_types=1);

namespace Fi1a\Http;

/**
 * JSON ответ
 */
interface JsonResponseInterface extends ContentResponseInterface
{
    /**
     * Опции используемые для кодирования данных в JSON
     */
    public function getEncodingOptions(): int;

    /**
     * Опции используемые для кодирования данных в JSON
     *
     * @return $this
     */
    public function setEncodingOptions(int $encodingOptions);

    /**
     * Установить данные
     *
     * @param mixed $data
     * @param HeaderCollectionInterface|string[]|string[][] $headers
     *
     * @return $this
     */
    public function data($data = [], ?int $status = null, $headers = []);
}

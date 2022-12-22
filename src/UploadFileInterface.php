<?php

declare(strict_types=1);

namespace Fi1a\Http;

use Fi1a\Collection\DataType\ValueObjectInterface;

/**
 * Файл
 */
interface UploadFileInterface extends ValueObjectInterface
{
    /**
     * Устанавливает значение ошибки
     *
     * @return $this
     */
    public function setError(int $error);

    /**
     * Возвращает ошибку
     */
    public function getError(): int;

    /**
     * Устанавливает имя
     *
     * @return $this
     */
    public function setName(string $name);

    /**
     * Возвращает имя
     */
    public function getName(): string;

    /**
     * Устанавливает тип
     *
     * @return $this
     */
    public function setType(string $type);

    /**
     * Возвращает тип
     */
    public function getType(): string;

    /**
     * Устанавливает путь
     *
     * @return $this
     */
    public function setTmpName(string $tmpName);

    /**
     * Возвращает путь
     */
    public function getTmpName(): string;

    /**
     * Устанавливает размер файла
     *
     * @return $this
     */
    public function setSize(int $size);

    /**
     * Возвращает размер файла
     */
    public function getSize(): int;
}

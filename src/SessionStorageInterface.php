<?php

declare(strict_types=1);

namespace Fi1a\Http;

use Fi1a\Collection\DataType\PathAccessInterface;

/**
 * Интерфейс хранения сессии
 */
interface SessionStorageInterface
{
    /**
     * Возврашает значения сесии
     *
     * @return PathAccessInterface|false
     */
    public function getValues();

    /**
     * Открывает сессию
     */
    public function open(): bool;

    /**
     * Закрывает сессию
     */
    public function close(): bool;

    /**
     * Регенерирует идентификатор
     */
    public function regenerate(bool $delete = false): bool;

    /**
     * Открыта сессия или нет
     */
    public function isOpen(): bool;

    /**
     * Очищает сессию
     */
    public function clear(): bool;

    /**
     * Возвращает идентификатор
     */
    public function getId(): string;

    /**
     * Устанавливает идентифкатор
     *
     * @return $this
     */
    public function setId(string $sessionId);

    /**
     * Возвращает название
     */
    public function getName(): string;

    /**
     * Устанавливает название
     *
     * @return $this
     */
    public function setName(string $name);
}

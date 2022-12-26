<?php

declare(strict_types=1);

namespace Fi1a\Http\Session;

use Fi1a\Collection\DataType\PathAccess;
use Fi1a\Collection\DataType\PathAccessInterface;

/**
 * Абстрактный класс хранения сессии
 */
abstract class AbstractSessionStorage implements SessionStorageInterface
{
    /**
     * @var PathAccessInterface|null
     */
    protected static $session;

    /**
     * @var bool
     */
    protected static $open = false;

    /**
     * @var bool
     */
    protected static $close = false;

    /**
     * Очищает сессию
     *
     * @return mixed[]
     */
    abstract protected function doClear(): array;

    /**
     * Открывает сессию
     *
     * @return mixed[]
     */
    abstract protected function doOpen(): array;

    /**
     * Закрывает сессию
     */
    abstract protected function doClose(): bool;

    /**
     * Регенерирует идентификатор
     *
     * @return mixed[]
     */
    abstract protected function doRegenerate(bool $delete = false): array;

    /**
     * @inheritDoc
     */
    public function getValues()
    {
        if (!$this->isOpen() || !static::$session) {
            return false;
        }

        return static::$session;
    }

    /**
     * @inheritDoc
     */
    public function open(): bool
    {
        if ($this->isOpen()) {
            return true;
        }

        static::$close = false;

        return static::$open = $this->init($this->doOpen());
    }

    /**
     * @inheritDoc
     */
    public function close(): bool
    {
        $return = $this->doClose();
        static::$close = true;
        static::$open = false;

        return $return;
    }

    /**
     * @inheritDoc
     */
    public function regenerate(bool $delete = false): bool
    {
        if (!$this->isOpen()) {
            return false;
        }

        return $this->init($this->doRegenerate($delete));
    }

    /**
     * @inheritDoc
     */
    public function isOpen(): bool
    {
        return static::$open;
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        if (!$this->isOpen()) {
            return false;
        }

        return $this->init($this->doClear());
    }

    /**
     * Инициализация
     *
     * @param mixed[] $session
     */
    protected function init(array $session): bool
    {
        static::$session = new PathAccess($session);

        return true;
    }
}

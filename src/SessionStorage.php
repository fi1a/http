<?php

declare(strict_types=1);

namespace Fi1a\Http;

use Fi1a\Collection\DataType\PathAccessInterface;
use RuntimeException;

use const PHP_SESSION_ACTIVE;

/**
 * Хранение сессии
 */
class SessionStorage extends AbstractSessionStorage
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

    public function __construct()
    {
        @ini_set('session.use_cookies', '1');
        session_register_shutdown();
    }

    /**
     * @inheritDoc
     */
    protected function doClear(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     * @psalm-suppress InvalidReturnType
     */
    protected function doOpen(): array
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            throw new RuntimeException('Сессия открыта php');
        }
        if ($this->headersSent($file, $line)) {
            throw new RuntimeException(
                sprintf('Заголовки были уже отправлены "%s:%d"', (string) $file, (int) $line)
            );
        }
        if ($this->sessionStart() === false) {
            throw new RuntimeException('Не удалось открыть сессию');
        }

        /** @psalm-suppress InvalidReturnStatement */
        return $_SESSION;
    }

    /**
     * Старт сессии
     */
    protected function sessionStart(): bool
    {
        return session_start();
    }

    /**
     * Отправлены заголовки или нет
     */
    protected function headersSent(?string &$file, ?int &$line): bool
    {
        return headers_sent($file, $line);
    }

    /**
     * @inheritDoc
     */
    protected function doClose(): bool
    {
        $values = $this->getValues();
        if ($values) {
            $_SESSION = $values->getArrayCopy();
        }
        session_write_close();

        return true;
    }

    /**
     * @inheritDoc
     * @psalm-suppress InvalidReturnType
     */
    protected function doRegenerate(bool $delete = false): array
    {
        if ($this->sessionStatus() !== PHP_SESSION_ACTIVE) {
            return [];
        }
        session_regenerate_id($delete);

        /** @psalm-suppress InvalidReturnStatement */
        return $_SESSION;
    }

    /**
     * Возвращает статус сессии
     */
    protected function sessionStatus(): int
    {
        return session_status();
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return session_id();
    }

    /**
     * @inheritDoc
     */
    public function setId(string $sessionId)
    {
        session_id($sessionId);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return session_name();
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name)
    {
        session_name($name);

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Fi1a\Http;

use ErrorException;
use Fi1a\Http\Session\SessionStorageInterface;

/**
 * Сохраняет значение в сессии. После получения значения, стирает его
 */
class Flush implements FlushInterface
{
    /**
     * @var SessionStorageInterface
     */
    private $session;

    public function __construct(?SessionStorageInterface $session = null)
    {
        if (!$session) {
            $session = session();
        }
        $this->session = $session;
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, $value)
    {
        $values = $this->session->getValues();
        if (!$values) {
            throw new ErrorException('Не удалось получить значения из сессии');
        }
        $values->set($key, $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, $default = null)
    {
        $values = $this->session->getValues();
        if (!$values) {
            throw new ErrorException('Не удалось получить значения из сессии');
        }
        /** @var mixed $value */
        $value = $values->get($key, $default);
        $values->delete($key);

        return $value;
    }
}

<?php

declare(strict_types=1);

namespace Fi1a\Http;

use Fi1a\Collection\DataType\ValueObject;
use InvalidArgumentException;
use LogicException;

use function preg_match;

/**
 * Cookie
 */
class Cookie extends ValueObject implements CookieInterface
{
    /**
     * @var string[]
     */
    protected $modelKeys = [
        'Name', 'Value', 'Domain', 'Path', 'Expires', 'Max-Age', 'Secure', 'HttpOnly', 'Session',
    ];

    /**
     * @inheritDoc
     */
    protected function getDefaultModelValues()
    {
        return [
            'Name' => null,
            'Value' => null,
            'Domain' => null,
            'Path' => '/',
            'Expires' => null,
            'Max-Age' => null,
            'Secure' => false,
            'HttpOnly' => false,
            'Session' => false,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getName(): ?string
    {
        /**
         * @var string|null $name
         */
        $name = $this->modelGet('Name');

        return $name;
    }

    /**
     * @inheritDoc
     */
    public function setName(?string $name)
    {
        if ($name === '') {
            throw new InvalidArgumentException('Имя не может быть пустым');
        }
        if (
            !is_null($name)
            && preg_match(
                '/[\x00-\x20\x22\x28-\x29\x2c\x2f\x3a-\x40\x5c\x7b\x7d\x7f]/',
                $name
            )
        ) {
            throw new InvalidArgumentException('Имя содержит недопустимые значения');
        }

        $this->modelSet('Name', $name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValue(): ?string
    {
        /**
         * @var string|null $value
         */
        $value = $this->modelGet('Value');

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function setValue(?string $value)
    {
        $this->modelSet('Value', $value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDomain(): ?string
    {
        /**
         * @var string|null $domain
         */
        $domain = $this->modelGet('Domain');

        return $domain;
    }

    /**
     * @inheritDoc
     */
    public function setDomain(?string $domain)
    {
        $this->modelSet('Domain', $domain);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return (string) $this->modelGet('Path');
    }

    /**
     * @inheritDoc
     */
    public function setPath(string $path)
    {
        $this->modelSet('Path', $path);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMaxAge(): ?int
    {
        /**
         * @var int|null $maxAge
         */
        $maxAge = $this->modelGet('Max-Age');

        return $maxAge;
    }

    /**
     * @inheritDoc
     */
    public function setMaxAge(?int $maxAge)
    {
        if ($maxAge && !$this->getExpires()) {
            $this->setExpires(time() + $maxAge);
        }
        if (is_null($maxAge) && $this->getExpires()) {
            $this->setExpires(null);
        }
        $this->modelSet('Max-Age', $maxAge);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExpires(): ?int
    {
        /**
         * @var int|null $expires
         */
        $expires = $this->modelGet('Expires');

        return $expires;
    }

    /**
     * @inheritDoc
     */
    public function setExpires($timestamp)
    {
        if (is_string($timestamp)) {
            $timestamp = strtotime($timestamp);
        }
        if (!is_null($timestamp)) {
            $timestamp = (int) $timestamp;
        }
        $this->modelSet('Expires', $timestamp);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isExpired(): bool
    {
        return !is_null($this->getExpires()) && time() >= $this->getExpires();
    }

    /**
     * @inheritDoc
     */
    public function getSecure(): bool
    {
        return (bool) $this->modelGet('Secure');
    }

    /**
     * @inheritDoc
     */
    public function setSecure(bool $secure)
    {
        $this->modelSet('Secure', $secure);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHttpOnly(): bool
    {
        return (bool) $this->modelGet('HttpOnly');
    }

    /**
     * @inheritDoc
     */
    public function setHttpOnly(bool $httpOnly)
    {
        $this->modelSet('HttpOnly', $httpOnly);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function validate(): void
    {
        if (!$this->getName()) {
            throw new LogicException('Название куки не может быть пустым');
        }
        if (is_null($this->getValue())) {
            throw new LogicException('Значение куки не может быть null');
        }
        if (!$this->getDomain()) {
            throw new LogicException('Домен куки не может быть пустым');
        }
    }
}

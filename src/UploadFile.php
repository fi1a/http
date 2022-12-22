<?php

declare(strict_types=1);

namespace Fi1a\Http;

use Fi1a\Collection\DataType\ValueObject;

/**
 * Файл
 */
class UploadFile extends ValueObject implements UploadFileInterface
{
    /**
     * @var string[]
     */
    protected $modelKeys = ['error', 'name', 'type', 'tmp_name', 'size',];

    /**
     * @inheritDoc
     */
    public function setError(int $error)
    {
        $this->modelSet('error', $error);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getError(): int
    {
        return (int) $this->modelGet('error');
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name)
    {
        $this->modelSet('name', $name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (string) $this->modelGet('name');
    }

    /**
     * @inheritDoc
     */
    public function setType(string $type)
    {
        $this->modelSet('type', $type);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return (string) $this->modelGet('type');
    }

    /**
     * @inheritDoc
     */
    public function setTmpName(string $tmpName)
    {
        $this->modelSet('tmp_name', $tmpName);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTmpName(): string
    {
        return (string) $this->modelGet('tmp_name');
    }

    /**
     * @inheritDoc
     */
    public function setSize(int $size)
    {
        $this->modelSet('size', $size);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSize(): int
    {
        return (int) $this->modelGet('size');
    }
}

<?php

declare(strict_types=1);

namespace Fi1a\Http;

/**
 * Ответ с содержанием
 */
class ContentResponse extends Response implements ContentResponseInterface
{
    /**
     * @var string
     */
    protected $content = '';

    /**
     * @inheritDoc
     */
    public function setContent(string $content)
    {
        $object = $this->getObject();

        $object->content = $content;

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return $this->content;
    }
}

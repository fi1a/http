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
    private $content = '';

    /**
     * @inheritDoc
     */
    public function setContent(string $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return $this->content;
    }
}

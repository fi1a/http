<?php

declare(strict_types=1);

namespace Fi1a\Http;

/**
 * Запрос
 */
class Request implements RequestInterface
{
    /**
     * @var UriInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $uri;

    /**
     * @inheritDoc
     */
    public function __construct(
        $uri,
        array $query = [],
        array $post = [],
        array $options = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        array $headers = [],
        $content = null
    ) {
        if (!($uri instanceof UriInterface)) {
            $uri = new Uri($uri);
        }
        $uri->withQueryParams($query);
        $this->setUri($uri);
    }

    /**
     * @inheritDoc
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * @inheritDoc
     */
    public function setUri(UriInterface $uri)
    {
        $this->uri = $uri;

        return $this;
    }
}

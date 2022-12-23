<?php

declare(strict_types=1);

namespace Fi1a\Http;

use Fi1a\Collection\DataType\PathAccess;
use Fi1a\Collection\DataType\PathAccessInterface;

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
     * @var PathAccessInterface
     */
    private $post;

    /**
     * @var UploadFileCollectionInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $files;

    /**
     * @var resource|string|null
     */
    private $content;

    /**
     * @var HttpCookieCollectionInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $cookies;

    /**
     * @var HeaderCollectionInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $headers;

    /**
     * @var ServerCollectionInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $server;

    /**
     * @var PathAccessInterface
     */
    private $options;

    /**
     * @inheritDoc
     */
    public function __construct(
        $uri,
        $query = [],
        $post = [],
        $options = [],
        ?HttpCookieCollectionInterface $cookies = null,
        ?UploadFileCollectionInterface $files = null,
        ?ServerCollectionInterface $server = null,
        ?HeaderCollectionInterface $headers = null,
        $content = null
    ) {
        $this->post = new PathAccess();
        $this->options = new PathAccess();
        if (!($uri instanceof UriInterface)) {
            $uri = new Uri($uri);
        }
        if (is_null($files)) {
            $files = new UploadFileCollection();
        }
        if (is_null($cookies)) {
            $cookies = new HttpCookieCollection();
        }
        if (is_null($headers)) {
            $headers = new HeaderCollection();
        }
        if (is_null($server)) {
            $server = new ServerCollection();
        }
        $uri->withQueryParams($query);
        $this->setUri($uri)
            ->setPost($post)
            ->setFiles($files)
            ->setContent($content)
            ->setCookies($cookies)
            ->setHeaders($headers)
            ->setServer($server)
            ->setOptions($options);
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

    /**
     * @inheritDoc
     */
    public function setPost($post)
    {
        if (!($post instanceof PathAccessInterface)) {
            $this->post->exchangeArray($post);

            return $this;
        }
        $this->post = $post;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPost(): PathAccessInterface
    {
        return $this->post;
    }

    /**
     * @inheritDoc
     */
    public function setQuery($query)
    {
        $this->uri->withQueryParams($query);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getQuery(): PathAccessInterface
    {
        return $this->uri->getQueryParams();
    }

    /**
     * @inheritDoc
     */
    public function setFiles(UploadFileCollectionInterface $files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFiles(): UploadFileCollectionInterface
    {
        return $this->files;
    }

    /**
     * @inheritDoc
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        $content = $this->content;
        if (!is_resource($content)) {
            $content = fopen('php://temp', 'r+');
            fwrite($content, (string) $this->content);
        }
        rewind($content);

        return $content;
    }

    /**
     * @inheritDoc
     */
    public function setCookies(HttpCookieCollectionInterface $cookies)
    {
        $this->cookies = $cookies;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCookies(): HttpCookieCollectionInterface
    {
        return $this->cookies;
    }

    /**
     * @inheritDoc
     */
    public function setHeaders(HeaderCollectionInterface $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): HeaderCollectionInterface
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    public function setServer(ServerCollectionInterface $server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getServer(): ServerCollectionInterface
    {
        return $this->server;
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): PathAccessInterface
    {
        return $this->options;
    }

    /**
     * @inheritDoc
     */
    public function setOptions($options)
    {
        if (!($options instanceof PathAccessInterface)) {
            $this->options->exchangeArray($options);

            return $this;
        }
        $this->options = $options;

        return $this;
    }
}

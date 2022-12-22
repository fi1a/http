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
     * @inheritDoc
     */
    public function __construct(
        $uri,
        $query = [],
        $post = [],
        array $options = [],
        array $cookies = [],
        ?UploadFileCollectionInterface $files = null,
        array $server = [],
        array $headers = [],
        $content = null
    ) {
        $this->post = new PathAccess();
        if (!($uri instanceof UriInterface)) {
            $uri = new Uri($uri);
        }
        if (is_null($files)) {
            $files = new UploadFileCollection();
        }
        $uri->withQueryParams($query);
        $this->setUri($uri)
            ->setPost($post)
            ->setFiles($files);
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
}

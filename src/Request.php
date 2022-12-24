<?php

declare(strict_types=1);

namespace Fi1a\Http;

use Fi1a\Collection\DataType\PathAccess;
use Fi1a\Collection\DataType\PathAccessInterface;

use const PREG_SPLIT_NO_EMPTY;

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
        if (is_null($server)) {
            $server = new ServerCollection();
        }

        $server->exchangeArray(
            array_replace([
                'SERVER_NAME' => 'localhost',
                'SERVER_PORT' => 80,
                'HTTP_HOST' => 'localhost',
                'HTTP_USER_AGENT' => '',
                'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'HTTP_ACCEPT_LANGUAGE' => 'ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4',
                'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
                'REMOTE_ADDR' => '127.0.0.1',
                'SCRIPT_NAME' => '',
                'SCRIPT_FILENAME' => '',
                'SERVER_PROTOCOL' => 'HTTP/1.1',
                'REQUEST_TIME' => time(),
                'REQUEST_METHOD' => 'GET',
            ], $server->getArrayCopy())
        );
        if (!($uri instanceof UriInterface)) {
            $uri = new Uri($uri);
        }
        if ($uri->getHost()) {
            $server->set('SERVER_NAME', $uri->getHost());
            $server->set('HTTP_HOST', $uri->getHost());
        }
        $port = $uri->getPort();
        if ($port) {
            $server->set('SERVER_PORT', $port);
            $server->set('HTTP_HOST', (string) $server->get('HTTP_HOST') . ':' . $port);
        }
        if (!$server->has('HTTPS')) {
            $server->set('HTTPS', $uri->isSecure() ? 'on' : 'off');
        }

        $this->post = new PathAccess();
        $this->options = new PathAccess();
        if (is_null($files)) {
            $files = new UploadFileCollection();
        }
        if (is_null($cookies)) {
            $cookies = new HttpCookieCollection();
        }
        if (is_null($headers)) {
            $headers = new HeaderCollection();
        }

        $rawHeader = $headers->getArrayCopy();
        /**
         * @var string|int $value
         */
        foreach ($server->getHeaders() as $name => $value) {
            array_unshift($rawHeader, [$name, $value,]);
        }
        $headers = new HeaderCollection($rawHeader);

        $uri->withQueryParams($query);
        $this->setUriInstance($uri)
            ->setPost($post)
            ->setFiles($files)
            ->setContent($content)
            ->setCookies($cookies)
            ->setHeaders($headers)
            ->setServer($server)
            ->setOptions($options);
    }

    /**
     * Возвращает Uri
     */
    protected function getUriInstance(): UriInterface
    {
        return $this->uri;
    }

    /**
     * Устанавливает Uri
     *
     * @return $this
     */
    protected function setUriInstance(UriInterface $uri)
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
        $this->getUriInstance()->withQueryParams($query);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getQuery(): PathAccessInterface
    {
        return $this->getUriInstance()->getQueryParams();
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

    /**
     * @inheritDoc
     */
    public function getClientIp(): string
    {
        $server = $this->getServer();

        return $server->has('REMOTE_ADDR') ? (string) $server->get('REMOTE_ADDR') : '';
    }

    /**
     * @inheritDoc
     */
    public function getScriptName(): string
    {
        $server = $this->getServer();

        return $server->has('SCRIPT_NAME') ? (string) $server->get('SCRIPT_NAME') : '';
    }

    /**
     * @inheritDoc
     */
    public function setPath(string $url)
    {
        $this->getUriInstance()->withPath($url);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return $this->getUriInstance()->getPath();
    }

    /**
     * @inheritDoc
     */
    public function getBasePath(): string
    {
        return $this->getUriInstance()->getBasePath();
    }

    /**
     * @inheritDoc
     */
    public function getNormalizedBasePath(): string
    {
        return $this->getUriInstance()->getNormalizedBasePath();
    }

    /**
     * @inheritDoc
     */
    public function setQueryString(string $query)
    {
        $this->getUriInstance()->withQuery($query);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getQueryString(): string
    {
        return $this->getUriInstance()->getQuery();
    }

    /**
     * @inheritDoc
     */
    public function getUser(): string
    {
        return $this->getUriInstance()->getUser();
    }

    /**
     * @inheritDoc
     */
    public function getPassword(): ?string
    {
        return $this->getUriInstance()->getPassword();
    }

    /**
     * @inheritDoc
     */
    public function getUserInfo(): string
    {
        return $this->getUriInstance()->getUserInfo();
    }

    /**
     * @inheritDoc
     */
    public function getPathAndQuery(): string
    {
        return $this->getUriInstance()->getPathAndQuery();
    }

    /**
     * @inheritDoc
     */
    public function getHost(): string
    {
        $header = $this->getHeaders()->getLastHeader('Host');
        if (!$header) {
            return '';
        }

        return mb_strtolower(preg_replace('/:\d+$/', '', trim((string) $header->getValue())));
    }

    /**
     * @inheritDoc
     */
    public function getSchemeAndHttpHost(): string
    {
        return $this->getScheme() . '://' . $this->getHttpHost();
    }

    /**
     * @inheritDoc
     */
    public function getUri(): string
    {
        return $this->getSchemeAndHttpHost() . $this->getPathAndQuery();
    }

    /**
     * @inheritDoc
     */
    public function getHttpHost(): string
    {
        $host = $this->getHost();
        $port = $this->getPort();
        $scheme = $this->getScheme();
        if (($scheme === 'http' && $port !== 80) || ($scheme === 'https' && $port !== 443)) {
            $host .= ':' . $port;
        }

        return $host;
    }

    /**
     * @inheritDoc
     */
    public function isSecure(): bool
    {
        $server = $this->getServer();

        return $server->has('HTTPS') && mb_strtolower((string) $server->get('HTTPS')) !== 'off';
    }

    /**
     * @inheritDoc
     */
    public function getScheme(): string
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    /**
     * @inheritDoc
     */
    public function getPort(): int
    {
        $server = $this->getServer();
        if ($server->has('SERVER_PORT')) {
            return (int) $server->get('SERVER_PORT');
        }

        return $this->isSecure() ? 443 : 80;
    }

    /**
     * @inheritDoc
     */
    public function setMethod(string $method)
    {
        $this->getServer()->set('REQUEST_METHOD', mb_strtoupper($method));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        $server = $this->getServer();

        return $server->has('REQUEST_METHOD')
            ? mb_strtoupper((string) $server->get('REQUEST_METHOD'))
            : HttpInterface::GET;
    }

    /**
     * @inheritDoc
     */
    public function isMethod(string $method): bool
    {
        return $this->getMethod() === mb_strtoupper($method);
    }

    /**
     * @inheritDoc
     */
    public function getContentType(): string
    {
        $header = $this->getHeaders()->getLastHeader('Content-Type');

        return $header ? (string) $header->getValue() : '';
    }

    /**
     * @inheritDoc
     */
    public function isNoCache(): bool
    {
        $header = $this->getHeaders()->getLastHeader('Pragma');

        return $header && $header->getValue() === 'no-cache';
    }

    /**
     * @inheritDoc
     */
    public function isXmlHttpRequest(): bool
    {
        $header = $this->getHeaders()->getLastHeader('X-Requested-With');

        return $header && $header->getValue() === 'XMLHttpRequest';
    }

    /**
     * @inheritDoc
     */
    public function getETags(): array
    {
        $header = $this->getHeaders()->getLastHeader('If-None-Match');
        if (!$header) {
            return [];
        }

        return preg_split('/\s*,\s*/', (string) $header->getValue(), -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * @inheritDoc
     */
    public function getScript(): string
    {
        return (string) $this->getServer()->get('SCRIPT_FILENAME');
    }
}

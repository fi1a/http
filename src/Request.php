<?php

declare(strict_types=1);

namespace Fi1a\Http;

use Fi1a\Collection\DataType\ArrayObjectInterface;
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
    private $rawBody;

    /**
     * @var mixed
     */
    private $body;

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
                'SERVER_PORT' => $server->has('HTTPS') && mb_strtolower((string) $server->get('HTTPS')) !== 'off'
                    ? 443
                    : 80,
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
            ->setRawBody($content)
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
     * Устанавливает POST
     *
     * @param mixed[]|PathAccessInterface $post
     *
     * @return $this
     */
    private function setPost($post)
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
    public function post(): PathAccessInterface
    {
        return clone $this->post;
    }

    /**
     * @inheritDoc
     */
    public function query(): PathAccessInterface
    {
        return clone $this->getUriInstance()->getQueryParams();
    }

    /**
     * @inheritDoc
     */
    public function all(): PathAccessInterface
    {
        $body = [];
        if ($this->body() instanceof ArrayObjectInterface) {
            /**
             * @var mixed[] $body
             * @psalm-suppress MixedMethodCall
             */
            $body = $this->body()->getArrayCopy();
        } elseif (is_array($this->body())) {
            /** @var mixed[] $body */
            $body = $this->body();
        }

        return new PathAccess(array_replace_recursive(
            $this->getUriInstance()->getQueryParams()->getArrayCopy(),
            $this->post->getArrayCopy(),
            $this->files->getArrayCopy(),
            $body
        ));
    }

    /**
     * @inheritDoc
     */
    public function only(array $keys): PathAccessInterface
    {
        $all = $this->all();
        $only = new PathAccess();
        foreach ($keys as $key) {
            if (!$all->has($key)) {
                continue;
            }

            $only->set($key, $all->get($key));
        }

        return $only;
    }

    /**
     * Устанавливает файлы
     *
     * @return $this
     */
    protected function setFiles(UploadFileCollectionInterface $files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function files(): UploadFileCollectionInterface
    {
        return clone $this->files;
    }

    /**
     * @inheritDoc
     */
    public function setRawBody($rawBody)
    {
        $this->rawBody = $rawBody;
        $body = $rawBody;
        if (is_resource($rawBody)) {
            $body = stream_get_contents($rawBody);
            rewind($rawBody);
        }
        $this->setBody($body);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function rawBody()
    {
        $rawBody = $this->rawBody;
        if (!is_resource($rawBody)) {
            $rawBody = fopen('php://temp', 'r+');
            fwrite($rawBody, (string) $this->rawBody);
        }
        rewind($rawBody);

        return $rawBody;
    }

    /**
     * @inheritDoc
     */
    public function body()
    {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Устанавливает cookies
     *
     * @return $this
     */
    protected function setCookies(HttpCookieCollectionInterface $cookies)
    {
        $this->cookies = $cookies;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function cookies(): HttpCookieCollectionInterface
    {
        return clone $this->cookies;
    }

    /**
     * Установить заголовки
     *
     * @return $this
     */
    protected function setHeaders(HeaderCollectionInterface $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function headers(): HeaderCollectionInterface
    {
        return clone $this->headers;
    }

    /**
     * Устанавливает значение SERVER
     *
     * @return $this
     */
    protected function setServer(ServerCollectionInterface $server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function server(): ServerCollectionInterface
    {
        return clone $this->server;
    }

    /**
     * @inheritDoc
     */
    public function options(): PathAccessInterface
    {
        return $this->options;
    }

    /**
     * Задает опции
     *
     * @param mixed[]|PathAccessInterface $options
     *
     * @return $this
     */
    protected function setOptions($options)
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
    public function clientIp(): string
    {
        $server = $this->server();

        return $server->has('REMOTE_ADDR') ? (string) $server->get('REMOTE_ADDR') : '';
    }

    /**
     * @inheritDoc
     */
    public function scriptName(): string
    {
        $server = $this->server();

        return $server->has('SCRIPT_NAME') ? (string) $server->get('SCRIPT_NAME') : '';
    }

    /**
     * @inheritDoc
     */
    public function path(): string
    {
        return $this->getUriInstance()->getPath();
    }

    /**
     * @inheritDoc
     */
    public function basePath(): string
    {
        return $this->getUriInstance()->getBasePath();
    }

    /**
     * @inheritDoc
     */
    public function normalizedBasePath(): string
    {
        return $this->getUriInstance()->getNormalizedBasePath();
    }

    /**
     * @inheritDoc
     */
    public function queryString(): string
    {
        return $this->getUriInstance()->getQuery();
    }

    /**
     * @inheritDoc
     */
    public function user(): string
    {
        return $this->getUriInstance()->getUser();
    }

    /**
     * @inheritDoc
     */
    public function password(): ?string
    {
        return $this->getUriInstance()->getPassword();
    }

    /**
     * @inheritDoc
     */
    public function userInfo(): string
    {
        return $this->getUriInstance()->getUserInfo();
    }

    /**
     * @inheritDoc
     */
    public function pathAndQuery(): string
    {
        return $this->getUriInstance()->getPathAndQuery();
    }

    /**
     * @inheritDoc
     */
    public function host(): string
    {
        /** @psalm-suppress PossiblyNullReference */
        return mb_strtolower(
            preg_replace(
                '/:\d+$/',
                '',
                trim((string) $this->headers()->getLastHeader('Host')->getValue())
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function schemeAndHttpHost(): string
    {
        return $this->scheme() . '://' . $this->httpHost();
    }

    /**
     * @inheritDoc
     */
    public function uri(): string
    {
        return $this->schemeAndHttpHost() . $this->pathAndQuery();
    }

    /**
     * @inheritDoc
     */
    public function httpHost(): string
    {
        $host = $this->host();
        $port = $this->port();
        $scheme = $this->scheme();
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
        $server = $this->server();

        return $server->has('HTTPS') && mb_strtolower((string) $server->get('HTTPS')) !== 'off';
    }

    /**
     * @inheritDoc
     */
    public function scheme(): string
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    /**
     * @inheritDoc
     */
    public function port(): int
    {
        return (int) $this->server()->get('SERVER_PORT');
    }

    /**
     * @inheritDoc
     */
    public function method(): string
    {
        $server = $this->server();

        return $server->has('REQUEST_METHOD')
            ? mb_strtoupper((string) $server->get('REQUEST_METHOD'))
            : HttpInterface::GET;
    }

    /**
     * @inheritDoc
     */
    public function isMethod(string $method): bool
    {
        return $this->method() === mb_strtoupper($method);
    }

    /**
     * @inheritDoc
     */
    public function contentType(): string
    {
        $header = $this->headers()->getLastHeader('Content-Type');

        return $header ? (string) $header->getValue() : '';
    }

    /**
     * @inheritDoc
     */
    public function isNoCache(): bool
    {
        $header = $this->headers()->getLastHeader('Pragma');

        return $header && $header->getValue() === 'no-cache';
    }

    /**
     * @inheritDoc
     */
    public function isXmlHttpRequest(): bool
    {
        $header = $this->headers()->getLastHeader('X-Requested-With');

        return $header && $header->getValue() === 'XMLHttpRequest';
    }

    /**
     * @inheritDoc
     */
    public function eTags(): array
    {
        $header = $this->headers()->getLastHeader('If-None-Match');
        if (!$header) {
            return [];
        }

        return preg_split('/\s*,\s*/', (string) $header->getValue(), -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * @inheritDoc
     */
    public function script(): string
    {
        return (string) $this->server()->get('SCRIPT_FILENAME');
    }
}

<?php

declare(strict_types=1);

namespace Fi1a\Http;

use Fi1a\Collection\DataType\PathAccess;
use Fi1a\Collection\DataType\PathAccessInterface;
use Fi1a\Format\Formatter;
use InvalidArgumentException;

/**
 * URI
 */
class Uri implements UriInterface
{
    /**
     * @var string
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected $scheme;

    /**
     * @var string
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected $user;

    /**
     * @var string|null
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected $password;

    /**
     * @var string
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected $host;

    /**
     * @var int|null
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected $port;

    /**
     * @var string
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected $path;

    /**
     * @var string
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected $query;

    /**
     * @var PathAccessInterface
     */
    protected $queryParams;

    /**
     * @var string
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected $fragment;

    /**
     * @var bool
     */
    protected $mutable = true;

    /**
     * @inheritDoc
     */
    public function __construct(string $uri = '', array $variables = [])
    {
        $this->queryParams = new PathAccess();
        $parsed = parse_url(Formatter::format($uri, $variables));

        $this->withScheme($parsed['scheme'] ?? 'https')
            ->withUserInfo($parsed['user'] ?? '', $parsed['pass'] ?? null)
            ->withHost($parsed['host'] ?? '')
            ->withPort($parsed['port'] ?? null)
            ->withPath($parsed['path'] ?? '')
            ->withQuery($parsed['query'] ?? '')
            ->withFragment($parsed['fragment'] ?? '');

        $this->mutable = false;
    }

    /**
     * @inheritDoc
     */
    public function scheme(): string
    {
        return $this->scheme;
    }

    /**
     * @inheritDoc
     */
    public function isSecure(): bool
    {
        return $this->scheme() === 'https';
    }

    /**
     * @inheritDoc
     */
    public function withScheme(string $scheme)
    {
        $object = $this->getObject();

        $scheme = mb_strtolower($scheme);
        if (!in_array($scheme, ['http', 'https'])) {
            throw new InvalidArgumentException(sprintf('Неизвестная схема "%s"', htmlspecialchars($scheme)));
        }

        $object->scheme = $scheme;

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function userInfo(): string
    {
        $userInfo = $this->user();
        $password = $this->password();
        if (!is_null($password)) {
            $userInfo .= ':' . $password;
        }

        return $userInfo;
    }

    /**
     * @inheritDoc
     */
    public function user(): string
    {
        return $this->user;
    }

    /**
     * @inheritDoc
     */
    public function password(): ?string
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function withUserInfo(string $user, ?string $password = null)
    {
        $object = $this->getObject();

        $object->user = $user;
        $object->password = $password;

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function host(): string
    {
        return $this->host;
    }

    /**
     * @inheritDoc
     */
    public function withHost(string $host)
    {
        $object = $this->getObject();

        $object->host = $host;

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function port(): ?int
    {
        return $this->port;
    }

    /**
     * @inheritDoc
     */
    public function withPort(?int $port)
    {
        $object = $this->getObject();

        $object->port = $port;

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function withPath(string $path)
    {
        $object = $this->getObject();

        if (!$path) {
            $path = '/';
        }
        $object->path = $path;

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function basePath(): string
    {
        $url = $this->path();
        $basename = basename($url);
        if (!$basename || !preg_match('/^(.+)\.(.+)$/i', $basename)) {
            return $url;
        }

        return mb_substr($url, 0, mb_strlen($url) - mb_strlen($basename));
    }

    /**
     * @inheritDoc
     */
    public function normalizedBasePath(): string
    {
        $basePath = $this->basePath();

        return rtrim($basePath, '/') . '/';
    }

    /**
     * @inheritDoc
     */
    public function query(): string
    {
        return $this->query;
    }

    /**
     * @inheritDoc
     */
    public function withQuery(string $query)
    {
        $object = $this->getObject();

        parse_str($query, $queryParams);
        $object->queryParams->exchangeArray($queryParams);
        $object->query = http_build_query($queryParams);

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function queryParams(): PathAccessInterface
    {
        return $this->queryParams;
    }

    /**
     * @inheritDoc
     */
    public function withQueryParams($queryParams)
    {
        $object = $this->getObject();

        if (!($queryParams instanceof PathAccessInterface)) {
            $object->queryParams->exchangeArray($queryParams);
        } else {
            $object->queryParams = $queryParams;
        }

        $object->query = http_build_query($object->queryParams->getArrayCopy());

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function fragment(): string
    {
        return $this->fragment;
    }

    /**
     * @inheritDoc
     */
    public function withFragment(string $fragment)
    {
        $object = $this->getObject();

        $object->fragment = $fragment;

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function authority(): string
    {
        if (!$this->host()) {
            return '';
        }
        $userInfo = $this->userInfo();
        $port = $this->port();

        return ($userInfo ? $userInfo . '@' : '') . $this->host() . ($port ? ':' . $port : '');
    }

    /**
     * @inheritDoc
     */
    public function url(): string
    {
        $authority = $this->authority();
        $url = '';
        if ($authority) {
            $url = $this->scheme() . '://' . $authority;
        }

        return $url . $this->path();
    }

    /**
     * @inheritDoc
     */
    public function uri(): string
    {
        $query = $this->query();
        $fragment = $this->fragment();

        return $this->url() . ($query ? '?' . $query : '') . ($fragment ? '#' . $fragment : '');
    }

    /**
     * @inheritDoc
     */
    public function pathAndQuery(): string
    {
        $query = $this->query();

        return $this->path() . ($query ? '?' . $query : '');
    }

    /**
     * @inheritDoc
     */
    public function maskedUri(): string
    {
        if (!$this->host()) {
            return '';
        }
        $userInfo = $this->userInfo();
        $port = $this->port();
        $query = $this->query();
        $fragment = $this->fragment();

        return $this->scheme() . '://' . ($userInfo ? '######:######@' : '')
            . $this->host() . ($port ? ':' . $port : '') . $this->path()
            . ($query ? '?' . $query : '') . ($fragment ? '#' . $fragment : '');
    }

    /**
     * @inheritDoc
     */
    public function replace(string $uri = '', array $variables = [])
    {
        $object = $this->getObject();

        $parsed = parse_url(Formatter::format($uri, $variables));
        if (isset($parsed['scheme'])) {
            $object = $object->withScheme($parsed['scheme']);
        }
        if (isset($parsed['user']) && isset($parsed['pass'])) {
            $object = $object->withUserInfo($parsed['user'], $parsed['pass']);
        }
        if (isset($parsed['host'])) {
            $object = $object->withHost($parsed['host']);
        }
        if (isset($parsed['port'])) {
            $object = $object->withPort($parsed['port']);
        }
        $object = $object->withPath($parsed['path'] ?? '')
            ->withQuery($parsed['query'] ?? '')
            ->withFragment($parsed['fragment'] ?? '');

        return $object;
    }

    /**
     * Возвращает объет для установки значений
     *
     * @return $this
     */
    protected function getObject()
    {
        return $this->mutable ? $this : clone $this;
    }

    /**
     * @inheritDoc
     */
    public function __clone()
    {
        $this->queryParams = clone $this->queryParams;
    }
}

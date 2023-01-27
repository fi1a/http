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
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @inheritDoc
     */
    public function isSecure(): bool
    {
        return $this->getScheme() === 'https';
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
    public function getUserInfo(): string
    {
        $userInfo = $this->getUser();
        $password = $this->getPassword();
        if (!is_null($password)) {
            $userInfo .= ':' . $password;
        }

        return $userInfo;
    }

    /**
     * @inheritDoc
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @inheritDoc
     */
    public function getPassword(): ?string
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
    public function getHost(): string
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
    public function getPort(): ?int
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
    public function getPath(): string
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
    public function getBasePath(): string
    {
        $url = $this->getPath();
        $basename = basename($url);
        if (!$basename || !preg_match('/^(.+)\.(.+)$/i', $basename)) {
            return $url;
        }

        return mb_substr($url, 0, mb_strlen($url) - mb_strlen($basename));
    }

    /**
     * @inheritDoc
     */
    public function getNormalizedBasePath(): string
    {
        $basePath = $this->getBasePath();

        return rtrim($basePath, '/') . '/';
    }

    /**
     * @inheritDoc
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @inheritDoc
     */
    public function withQuery(string $query)
    {
        $object = $this->getObject();

        $object->query = $query;
        parse_str($query, $queryParams);
        $object->queryParams->exchangeArray($queryParams);

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function getQueryParams(): PathAccessInterface
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
    public function getFragment(): string
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
    public function getAuthority(): string
    {
        if (!$this->getHost()) {
            return '';
        }
        $userInfo = $this->getUserInfo();
        $port = $this->getPort();

        return ($userInfo ? $userInfo . '@' : '') . $this->getHost() . ($port ? ':' . $port : '');
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        $authority = $this->getAuthority();
        $url = '';
        if ($authority) {
            $url = $this->getScheme() . '://' . $authority;
        }

        return $url . $this->getPath();
    }

    /**
     * @inheritDoc
     */
    public function getUri(): string
    {
        $query = $this->getQuery();
        $fragment = $this->getFragment();

        return $this->getUrl() . ($query ? '?' . $query : '') . ($fragment ? '#' . $fragment : '');
    }

    /**
     * @inheritDoc
     */
    public function getPathAndQuery(): string
    {
        $query = $this->getQuery();

        return $this->getPath() . ($query ? '?' . $query : '');
    }

    /**
     * @inheritDoc
     */
    public function getMaskedUri(): string
    {
        if (!$this->getHost()) {
            return '';
        }
        $userInfo = $this->getUserInfo();
        $port = $this->getPort();
        $query = $this->getQuery();
        $fragment = $this->getFragment();

        return $this->getScheme() . '://' . ($userInfo ? '######:######@' : '')
            . $this->getHost() . ($port ? ':' . $port : '') . $this->getPath()
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

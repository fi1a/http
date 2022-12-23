<?php

declare(strict_types=1);

namespace Fi1a\Http;

/**
 * Cookie
 */
class HttpCookie extends Cookie implements HttpCookieInterface
{
    /**
     * @var string[]
     */
    protected $modelKeys = [
        'Name', 'Value', 'Domain', 'Path', 'Expires', 'Max-Age', 'Secure', 'HttpOnly', 'NeedSet',
    ];

    /**
     * @inheritDoc
     */
    protected function getDefaultModelValues()
    {
        return array_merge(parent::getDefaultModelValues(), [
            'NeedSet' => true,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function setName(?string $name)
    {
        $oldValue = (string) $this->modelGet('Name');

        parent::setName($name);

        if ($name !== $oldValue) {
            $this->setNeedSet(true);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setValue(?string $value)
    {
        if ($value !== $this->modelGet('Value')) {
            $this->setNeedSet(true);
        }

        return parent::setValue($value);
    }

    /**
     * @inheritDoc
     */
    public function setDomain(?string $domain)
    {
        if ($domain !== $this->modelGet('Domain')) {
            $this->setNeedSet(true);
        }

        return parent::setDomain($domain);
    }

    /**
     * @inheritDoc
     */
    public function setPath(string $path)
    {
        if ($path !== $this->modelGet('Path')) {
            $this->setNeedSet(true);
        }

        return parent::setPath($path);
    }

    /**
     * @inheritDoc
     */
    public function setMaxAge(?int $maxAge)
    {
        if ($maxAge !== $this->modelGet('Max-Age')) {
            $this->setNeedSet(true);
        }

        return parent::setMaxAge($maxAge);
    }

    /**
     * @inheritDoc
     */
    public function setExpires($timestamp)
    {
        $oldValue = (int) $this->modelGet('Expires');

        parent::setExpires($timestamp);

        if ($this->modelGet('Expires') !== $oldValue) {
            $this->setNeedSet(true);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSecure(bool $secure)
    {
        if ($secure !== $this->modelGet('Secure')) {
            $this->setNeedSet(true);
        }

        return parent::setSecure($secure);
    }

    /**
     * @inheritDoc
     */
    public function setHttpOnly(bool $httpOnly)
    {
        if ($httpOnly !== $this->modelGet('HttpOnly')) {
            $this->setNeedSet(true);
        }

        return parent::setHttpOnly($httpOnly);
    }

    /**
     * @inheritDoc
     */
    public function setNeedSet(bool $needSet = false)
    {
        $this->modelSet('NeedSet', $needSet);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getNeedSet(): bool
    {
        return (bool) $this->modelGet('NeedSet');
    }
}

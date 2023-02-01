<?php

declare(strict_types=1);

namespace Fi1a\Http;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;

/**
 * Ответ
 */
class Response implements ResponseInterface
{
    /**
     * @var string[]
     */
    protected static $reasonPhrases = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Reserved for WebDAV advanced collections expired proposal',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates (Experimental)',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    /**
     * @var int
     */
    protected $status = self::HTTP_OK;

    /**
     * @var string|null
     */
    protected $reasonPhrase = 'OK';

    /**
     * @var HeaderCollectionInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected $headers;

    /**
     * @var string
     */
    protected $httpVersion = '1.0';

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var string
     */
    protected $charset = 'utf-8';

    /**
     * @var HttpCookieCollectionInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected $cookies;

    /**
     * @var bool
     */
    protected $mutable = true;

    public function __construct(
        int $status = self::HTTP_OK,
        ?HeaderCollectionInterface $headers = null,
        ?RequestInterface $request = null
    ) {
        if (is_null($request)) {
            $request = request();
        }
        $this->request = $request;
        if (is_null($headers)) {
            $headers = new HeaderCollection();
        }
        $this->withHeaders($headers);
        $this->withStatus($status);
        $this->withCookies($request->cookies());
        if (!$this->hasHeader('Date')) {
            $this->withDate(new DateTime());
        }

        $this->mutable = false;
    }

    /**
     * @inheritDoc
     */
    public function withStatus(int $status, ?string $reasonPhrase = null)
    {
        $object = $this->getObject();

        if ($status < 100 || $status >= 600) {
            throw new InvalidArgumentException(
                sprintf('Ошибка в коде статуса HTTP ответа "%d"', $status)
            );
        }
        $object->status = $status;
        if (is_null($reasonPhrase) && isset(static::$reasonPhrases[$status])) {
            $reasonPhrase = static::$reasonPhrases[$status];
        }
        $object->reasonPhrase = $reasonPhrase;

        return $object->prepare();
    }

    /**
     * @inheritDoc
     */
    public function status(): int
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function reasonPhrase(): ?string
    {
        return $this->reasonPhrase;
    }

    /**
     * @inheritDoc
     */
    public function withHeaders(HeaderCollectionInterface $headers)
    {
        $object = $this->getObject();

        $object->headers = $headers;

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function headers(): HeaderCollectionInterface
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    public function withHeader(string $name, string $value)
    {
        $object = $this->getObject();

        $object->headers->add([$name, $value]);

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function withoutHeader(string $name)
    {
        $object = $this->getObject();

        $object->headers->withoutHeader($name);

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function hasHeader(string $name): bool
    {
        return $this->headers->hasHeader($name);
    }

    /**
     * @inheritDoc
     */
    public function withHttpVersion(string $version)
    {
        $object = $this->getObject();

        $object->httpVersion = $version;

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function httpVersion(): string
    {
        return $this->httpVersion;
    }

    /**
     * @inheritDoc
     */
    public function isEmpty(): bool
    {
        /** @var int[] $statusCodes */
        static $statusCodes = [
            self::HTTP_NO_CONTENT,
            self::HTTP_NOT_MODIFIED,
        ];

        return in_array($this->status(), $statusCodes);
    }

    /**
     * @inheritDoc
     */
    public function isInformational(): bool
    {
        return $this->status() >= 100 && $this->status() < 200;
    }

    /**
     * @inheritDoc
     */
    public function isSuccessful(): bool
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    /**
     * @inheritDoc
     */
    public function isClientError(): bool
    {
        return $this->status() >= 300 && $this->status() < 500;
    }

    /**
     * @inheritDoc
     */
    public function isServerError(): bool
    {
        return $this->status() >= 500 && $this->status() < 600;
    }

    /**
     * @inheritDoc
     */
    public function isOk(): bool
    {
        return $this->status() === static::HTTP_OK;
    }

    /**
     * @inheritDoc
     */
    public function isForbidden(): bool
    {
        return $this->status() === static::HTTP_FORBIDDEN;
    }

    /**
     * @inheritDoc
     */
    public function isNotFound(): bool
    {
        return $this->status() === static::HTTP_NOT_FOUND;
    }

    /**
     * @inheritDoc
     */
    public function isRedirection(?string $location = null): bool
    {
        /** @var int[] $statusCodes */
        static $statusCodes = [
            self::HTTP_CREATED,
            self::HTTP_MOVED_PERMANENTLY,
            self::HTTP_FOUND,
            self::HTTP_SEE_OTHER,
            self::HTTP_TEMPORARY_REDIRECT,
            self::HTTP_PERMANENTLY_REDIRECT,
        ];
        $header = $this->headers()->getLastHeader('Location');

        return in_array($this->status(), $statusCodes)
            && (is_null($location) || !$header || $location === $header->getValue());
    }

    /**
     * @inheritDoc
     */
    public function withCharset(string $charset)
    {
        $object = $this->getObject();

        $object->charset = $charset;
        $object = $object->withoutHeader('Content-Type')
            ->prepare();

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function charset(): string
    {
        return $this->charset;
    }

    /**
     * @inheritDoc
     */
    public function withDate(DateTime $date)
    {
        $object = $this->getObject();

        $date = clone $date;
        $date->setTimezone(new DateTimeZone('UTC'));
        $object = $object->withoutHeader('Date');
        $object = $object->withHeader('Date', $date->format('D, d M Y H:i:s') . ' GMT');

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function date(): DateTime
    {
        /**
         * @psalm-suppress PossiblyNullArgument
         * @psalm-suppress PossiblyNullReference
         */
        return DateTime::createFromFormat(
            'D, d M Y H:i:s e',
            $this->headers()->getLastHeader('Date')->getValue()
        );
    }

    /**
     * @inheritDoc
     */
    public function lastModified(): ?DateTime
    {
        if (!$this->hasHeader('Last-Modified')) {
            return null;
        }

        /**
         * @psalm-suppress PossiblyNullArgument
         * @psalm-suppress PossiblyNullReference
         */
        return DateTime::createFromFormat(
            'D, d M Y H:i:s e',
            $this->headers()->getLastHeader('Last-Modified')->getValue()
        );
    }

    /**
     * @inheritDoc
     */
    public function withLastModified(?DateTime $date = null)
    {
        $object = $this->getObject();

        $object = $object->withoutHeader('Last-Modified');
        if (is_null($date)) {
            return $object;
        }

        $date = clone $date;
        $date->setTimezone(new DateTimeZone('UTC'));
        $object = $object->withHeader('Last-Modified', $date->format('D, d M Y H:i:s') . ' GMT');

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function withCookies(HttpCookieCollectionInterface $cookies)
    {
        $object = $this->getObject();

        $object->cookies = $cookies;

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function cookies(): HttpCookieCollectionInterface
    {
        return $this->cookies;
    }

    /**
     * Подготавливает ответ на основе запроса
     *
     * @return $this
     */
    protected function prepare()
    {
        $object = $this->getObject();

        $object = $object->isInformational() || $object->isEmpty()
            ? $object->prepareInformation()
            : $object->prepareDefault();

        $server = $object->request->server();
        if ($server->get('SERVER_PROTOCOL') !== 'HTTP/1.0') {
            $object = $object->withHttpVersion('1.1');
        }

        return $object;
    }

    /**
     * Для всех остальных по умолчанию
     *
     * @return $this
     */
    protected function prepareDefault()
    {
        $object = $this->getObject();

        if (!$object->hasHeader('Content-Type')) {
            $object = $object->withHeader('Content-Type', 'text/html; charset=' . $this->charset());
        }
        if ($object->hasHeader('Transfer-Encoding') && $this->hasHeader('Content-Length')) {
            $object = $object->withoutHeader('Content-Length');
        }

        return $object;
    }

    /**
     * Для пустого или информационного ответа
     *
     * @return $this
     */
    protected function prepareInformation()
    {
        return $this->getObject()
            ->withoutHeader('Content-Type')
            ->withoutHeader('Content-Length');
    }

    /**
     * @param HeaderCollectionInterface|string[]|string[][] $headers
     *
     * @return $this
     */
    protected function useHeaders($headers)
    {
        $object = $this->getObject();

        if (!is_array($headers) && !($headers instanceof HeaderCollectionInterface)) {
            throw new InvalidArgumentException(
                'Заголовки должны быть массивом или реализовывать ' . HeaderCollectionInterface::class
            );
        }
        if ($headers instanceof HeaderCollectionInterface) {
            $object = $object->withHeaders($headers);
        }
        if (is_array($headers) && count($headers)) {
            foreach ($headers as $name => $value) {
                $header = $value;
                if (is_string($name) && !is_array($value)) {
                    $header = [
                        $name,
                        $value,
                    ];
                }
                $object->headers()->add($header);
            }
        }

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
        $this->headers = clone $this->headers;
        $this->cookies = clone $this->cookies;
    }
}

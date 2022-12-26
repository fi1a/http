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
    private static $reasonPhrases = [
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
    private $status = self::HTTP_OK;

    /**
     * @var string|null
     */
    private $reasonPhrase = 'OK';

    /**
     * @var HeaderCollectionInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $headers;

    /**
     * @var string
     */
    private $httpVersion = '1.0';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var string
     */
    private $charset = 'utf-8';

    /**
     * @var HttpCookieCollectionInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $cookies;

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
        $this->setStatus($status);
        $this->setCookies($request->cookies());
    }

    /**
     * @inheritDoc
     */
    public function setStatus(int $status, ?string $reasonPhrase = null)
    {
        if ($status < 100 || $status >= 600) {
            throw new InvalidArgumentException(
                sprintf('Ошибка в коде статуса HTTP ответа "%d"', $status)
            );
        }
        $this->status = $status;
        if (is_null($reasonPhrase) && isset(static::$reasonPhrases[$status])) {
            $reasonPhrase = static::$reasonPhrases[$status];
        }
        $this->reasonPhrase = $reasonPhrase;
        $this->prepare();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function getReasonPhrase(): ?string
    {
        return $this->reasonPhrase;
    }

    /**
     * @inheritDoc
     */
    public function withHeaders(HeaderCollectionInterface $headers)
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
    public function withHeader(string $name, string $value)
    {
        $this->headers->add([$name, $value]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withoutHeader(string $name)
    {
        $this->headers->withoutHeader($name);

        return $this;
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
    public function setHttpVersion(string $version)
    {
        $this->httpVersion = $version;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHttpVersion(): string
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

        return in_array($this->getStatus(), $statusCodes);
    }

    /**
     * @inheritDoc
     */
    public function isInformational(): bool
    {
        return $this->getStatus() >= 100 && $this->getStatus() < 200;
    }

    /**
     * @inheritDoc
     */
    public function isSuccessful(): bool
    {
        return $this->getStatus() >= 200 && $this->getStatus() < 300;
    }

    /**
     * @inheritDoc
     */
    public function isClientError(): bool
    {
        return $this->getStatus() >= 300 && $this->getStatus() < 500;
    }

    /**
     * @inheritDoc
     */
    public function isServerError(): bool
    {
        return $this->getStatus() >= 500 && $this->getStatus() < 600;
    }

    /**
     * @inheritDoc
     */
    public function isOk(): bool
    {
        return $this->getStatus() === static::HTTP_OK;
    }

    /**
     * @inheritDoc
     */
    public function isForbidden(): bool
    {
        return $this->getStatus() === static::HTTP_FORBIDDEN;
    }

    /**
     * @inheritDoc
     */
    public function isNotFound(): bool
    {
        return $this->getStatus() === static::HTTP_NOT_FOUND;
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
        $header = $this->getHeaders()->getLastHeader('Location');

        return in_array($this->getStatus(), $statusCodes)
            && (is_null($location) || !$header || $location === $header->getValue());
    }

    /**
     * @inheritDoc
     */
    public function setCharset(string $charset)
    {
        $this->charset = $charset;
        $this->withoutHeader('Content-Type');
        $this->prepare();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCharset(): string
    {
        return $this->charset;
    }

    /**
     * @inheritDoc
     */
    public function setDate(DateTime $date)
    {
        $date = clone $date;
        $date->setTimezone(new DateTimeZone('UTC'));
        $this->withoutHeader('Date');
        $this->withHeader('Date', $date->format('D, d M Y H:i:s') . ' GMT');

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDate(): DateTime
    {
        if (!$this->hasHeader('Date')) {
            $this->setDate(new DateTime());
        }

        /**
         * @psalm-suppress PossiblyNullArgument
         * @psalm-suppress PossiblyNullReference
         */
        return DateTime::createFromFormat(
            'D, d M Y H:i:s e',
            $this->getHeaders()->getLastHeader('Date')->getValue()
        );
    }

    /**
     * @inheritDoc
     */
    public function getLastModified(): ?DateTime
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
            $this->getHeaders()->getLastHeader('Last-Modified')->getValue()
        );
    }

    /**
     * @inheritDoc
     */
    public function setLastModified(?DateTime $date = null)
    {
        $this->withoutHeader('Last-Modified');
        if (is_null($date)) {
            return $this;
        }

        $date = clone $date;
        $date->setTimezone(new DateTimeZone('UTC'));
        $this->withHeader('Last-Modified', $date->format('D, d M Y H:i:s') . ' GMT');

        return $this;
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
    public function cookies(): HttpCookieCollectionInterface
    {
        return $this->cookies;
    }

    /**
     * Подготавливает ответ на основе запроса
     */
    private function prepare(): void
    {
        $this->isInformational() || $this->isEmpty()
            ? $this->prepareInformation()
            : $this->prepareDefault();

        $server = $this->request->server();
        if ($server->get('SERVER_PROTOCOL') !== 'HTTP/1.0') {
            $this->setHttpVersion('1.1');
        }
    }

    /**
     * Для всех остальных по умолчанию
     */
    private function prepareDefault(): void
    {
        if (!$this->hasHeader('Content-Type')) {
            $this->withHeader('Content-Type', 'text/html; charset=' . $this->getCharset());
        }
        if ($this->hasHeader('Transfer-Encoding') && $this->hasHeader('Content-Length')) {
            $this->withoutHeader('Content-Length');
        }
    }

    /**
     * Для пустого или информационного ответа
     */
    private function prepareInformation(): void
    {
        $this->withoutHeader('Content-Type');
        $this->withoutHeader('Content-Length');
    }

    /**
     * @param HeaderCollectionInterface|string[]|string[][] $headers
     */
    protected function useHeaders($headers): void
    {
        if (!is_array($headers) && !($headers instanceof HeaderCollectionInterface)) {
            throw new InvalidArgumentException(
                'Заголовки должны быть массивом или реализовывать ' . HeaderCollectionInterface::class
            );
        }
        if ($headers instanceof HeaderCollectionInterface) {
            $this->withHeaders($headers);
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
                $this->getHeaders()->add($header);
            }
        }
    }
}

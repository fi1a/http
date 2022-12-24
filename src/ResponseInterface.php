<?php

declare(strict_types=1);

namespace Fi1a\Http;

use DateTime;

/**
 * Интерфейс ответа
 */
interface ResponseInterface
{
    public const HTTP_CONTINUE = 100;

    public const HTTP_SWITCHING_PROTOCOLS = 101;

    public const HTTP_PROCESSING = 102;

    public const HTTP_OK = 200;

    public const HTTP_CREATED = 201;

    public const HTTP_ACCEPTED = 202;

    public const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;

    public const HTTP_NO_CONTENT = 204;

    public const HTTP_RESET_CONTENT = 205;

    public const HTTP_PARTIAL_CONTENT = 206;

    public const HTTP_MULTI_STATUS = 207;

    public const HTTP_ALREADY_REPORTED = 208;

    public const HTTP_IM_USED = 226;

    public const HTTP_MULTIPLE_CHOICES = 300;

    public const HTTP_MOVED_PERMANENTLY = 301;

    public const HTTP_FOUND = 302;

    public const HTTP_SEE_OTHER = 303;

    public const HTTP_NOT_MODIFIED = 304;

    public const HTTP_USE_PROXY = 305;

    public const HTTP_RESERVED = 306;

    public const HTTP_TEMPORARY_REDIRECT = 307;

    public const HTTP_PERMANENTLY_REDIRECT = 308;

    public const HTTP_BAD_REQUEST = 400;

    public const HTTP_UNAUTHORIZED = 401;

    public const HTTP_PAYMENT_REQUIRED = 402;

    public const HTTP_FORBIDDEN = 403;

    public const HTTP_NOT_FOUND = 404;

    public const HTTP_METHOD_NOT_ALLOWED = 405;

    public const HTTP_NOT_ACCEPTABLE = 406;

    public const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;

    public const HTTP_REQUEST_TIMEOUT = 408;

    public const HTTP_CONFLICT = 409;

    public const HTTP_GONE = 410;

    public const HTTP_LENGTH_REQUIRED = 411;

    public const HTTP_PRECONDITION_FAILED = 412;

    public const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;

    public const HTTP_REQUEST_URI_TOO_LONG = 414;

    public const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;

    public const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;

    public const HTTP_EXPECTATION_FAILED = 417;

    public const HTTP_I_AM_A_TEAPOT = 418;

    public const HTTP_UNPROCESSABLE_ENTITY = 422;

    public const HTTP_LOCKED = 423;

    public const HTTP_FAILED_DEPENDENCY = 424;

    public const HTTP_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL = 425;

    public const HTTP_UPGRADE_REQUIRED = 426;

    public const HTTP_PRECONDITION_REQUIRED = 428;

    public const HTTP_TOO_MANY_REQUESTS = 429;

    public const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;

    public const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;

    public const HTTP_INTERNAL_SERVER_ERROR = 500;

    public const HTTP_NOT_IMPLEMENTED = 501;

    public const HTTP_BAD_GATEWAY = 502;

    public const HTTP_SERVICE_UNAVAILABLE = 503;

    public const HTTP_GATEWAY_TIMEOUT = 504;

    public const HTTP_VERSION_NOT_SUPPORTED = 505;

    public const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;

    public const HTTP_INSUFFICIENT_STORAGE = 507;

    public const HTTP_LOOP_DETECTED = 508;

    public const HTTP_NOT_EXTENDED = 510;

    public const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;

    public function __construct(
        int $status = self::HTTP_OK,
        ?HeaderCollectionInterface $headers = null,
        ?RequestInterface $request = null
    );

    /**
     * Устанавливает код и текст ответа
     *
     * @return $this
     */
    public function setStatus(int $status, ?string $reasonPhrase = null);

    /**
     * Возвращает код ответа
     */
    public function getStatus(): int;

    /**
     * Возвращает текст ответа
     */
    public function getReasonPhrase(): ?string;

    /**
     * Устанавливает заголовки
     *
     * @return $this
     */
    public function withHeaders(HeaderCollectionInterface $headers);

    /**
     * Возвращает заголовки
     */
    public function getHeaders(): HeaderCollectionInterface;

    /**
     * Добавляет заголовок с определенным именем и значением
     *
     * @return $this
     */
    public function withHeader(string $name, string $value);

    /**
     * Удалить заголовки с определенным именем
     *
     * @return $this
     */
    public function withoutHeader(string $name);

    /**
     * Проверяет наличие заголовки
     */
    public function hasHeader(string $name): bool;

    /**
     * Устанавливает версию HTTP протокола
     *
     * @return $this
     */
    public function setHttpVersion(string $version);

    /**
     * Возвращает HTTP версию протокола
     */
    public function getHttpVersion(): string;

    /**
     * Если true, то ответ пустой
     */
    public function isEmpty(): bool;

    /**
     * Если true, то ответ информационный
     */
    public function isInformational(): bool;

    /**
     * Если true, то ответ успешный
     */
    public function isSuccessful(): bool;

    /**
     * Если true, то клиентская ошибка
     */
    public function isClientError(): bool;

    /**
     * Если true, то серверная ошибка
     */
    public function isServerError(): bool;

    /**
     * Если true, то ответ 200 OK
     */
    public function isOk(): bool;

    /**
     * Если true, то 403 Forbidden
     */
    public function isForbidden(): bool;

    /**
     * Если true, то 404 Not found
     */
    public function isNotFound(): bool;

    /**
     * Если true, то перенаправление
     *
     * Если передать параметр $location, то проверяет происходит ли перенаправление на этот адрес
     */
    public function isRedirection(?string $location = null): bool;

    /**
     * Устанавливает кодировку
     *
     * @return $this
     */
    public function setCharset(string $charset);

    /**
     * Возвращает кодировку
     */
    public function getCharset(): string;

    /**
     * Устанавливает дату
     *
     * @return $this
     */
    public function setDate(DateTime $date);

    /**
     * Возвращает дату
     */
    public function getDate(): DateTime;

    /**
     * Возвращает время последнего изменения
     */
    public function getLastModified(): ?DateTime;

    /**
     * Устанавливает время последнего изменения
     *
     * @return $this
     */
    public function setLastModified(?DateTime $date = null);
}

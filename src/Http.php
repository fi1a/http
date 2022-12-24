<?php

declare(strict_types=1);

namespace Fi1a\Http;

use const PHP_URL_PATH;

/**
 * Менеджер
 */
class Http implements HttpInterface
{
    /**
     * @var RequestInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $request;

    /**
     * @var SessionStorageInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $session;

    public function __construct(RequestInterface $request, SessionStorageInterface $session)
    {
        $this->request($request);
        $this->session($session);
    }

    /**
     * @inheritDoc
     */
    public function request(?RequestInterface $request = null): RequestInterface
    {
        if (!is_null($request)) {
            $this->request = $request;
        }

        return $this->request;
    }

    /**
     * @inheritDoc
     */
    public function session(?SessionStorageInterface $session = null): SessionStorageInterface
    {
        if (!is_null($session)) {
            $this->session = $session;
        }

        return $this->session;
    }

    /**
     * @inheritDoc
     */
    public static function createRequestWithGlobals(
        array $query,
        array $post,
        array $options,
        array $cookies,
        array $files,
        array $server
    ): RequestInterface {
        $converted = [];
        /** @var string $part */
        foreach ($files as $part => $values) {
            $names = $types = $tmp = $errors = $sizes = [];
            self::convertFileArray($values['name'] ?? [], $part, $names);
            self::convertFileArray($values['type'] ?? [], $part, $types);
            self::convertFileArray($values['tmp_name'] ?? [], $part, $tmp);
            self::convertFileArray($values['error'] ?? [], $part, $errors);
            self::convertFileArray($values['size'] ?? [], $part, $sizes);

            /** @var string $value */
            foreach ($names as $path => $value) {
                /** @psalm-suppress MixedAssignment */
                $converted[$path]['name'] = $value;
                /** @psalm-suppress MixedAssignment */
                $converted[$path]['type'] = $types[$path];
                /** @psalm-suppress MixedAssignment */
                $converted[$path]['tmp_name'] = $tmp[$path];
                /** @psalm-suppress MixedAssignment */
                $converted[$path]['error'] = $errors[$path];
                /** @psalm-suppress MixedAssignment */
                $converted[$path]['size'] = $sizes[$path];
            }
        }
        $uploadFiles = new UploadFileCollection();
        foreach ($converted as $path => $uploadFileData) {
            $uploadFiles->set($path, new UploadFile($uploadFileData));
        }

        $cookieCollection = new HttpCookieCollection();
        /**
         * @var string|null $value
         */
        foreach ($cookies as $name => $value) {
            $cookieCollection[] = [
                'Name' => $name,
                'Value' => $value,
            ];
        }
        $cookieCollection->setNeedSet(false);

        $serverCollection = new ServerCollection($server);
        $headers = new HeaderCollection();

        return static::requestFactory(
            parse_url($server['REQUEST_URI'] ?? '/', PHP_URL_PATH),
            $query,
            $post,
            $options,
            $cookieCollection,
            $uploadFiles,
            $serverCollection,
            $headers,
            fopen('php://input', 'rb')
        );
    }

    /**
     * Конвертация массива файлов
     *
     * @param mixed $files
     * @param mixed[] $converted
     */
    private static function convertFileArray($files, string $path, array &$converted): void
    {
        if (!is_array($files)) {
            /** @psalm-suppress MixedAssignment */
            $converted[$path] = $files;

            return;
        }
        /** @var mixed $values */
        foreach ($files as $part => $values) {
            self::convertFileArray($values, $path . ($path ? ':' : '') . $part, $converted);
        }
    }

    /**
     * Фабрика экземпляра класса запроса
     *
     * @param mixed[]  $query
     * @param mixed[]  $post
     * @param mixed[]  $options
     * @param resource $content
     */
    private static function requestFactory(
        string $url,
        array $query,
        array $post,
        array $options,
        HttpCookieCollectionInterface $cookies,
        UploadFileCollectionInterface $files,
        ServerCollectionInterface $server,
        HeaderCollectionInterface $headers,
        $content
    ): RequestInterface {
        return new Request(
            $url,
            $query,
            $post,
            $options,
            $cookies,
            $files,
            $server,
            $headers,
            $content
        );
    }
}

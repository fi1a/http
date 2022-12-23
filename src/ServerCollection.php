<?php

declare(strict_types=1);

namespace Fi1a\Http;

use Fi1a\Collection\Collection;

/**
 * Коллекция значений SERVER
 */
class ServerCollection extends Collection implements ServerCollectionInterface
{
    /**
     * @inheritDoc
     */
    public function __construct(?array $data = null)
    {
        parent::__construct('mixed', $data);
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        /**
         * @var string[] $content
         */
        static $content = ['CONTENT_LENGTH', 'CONTENT_MD5', 'CONTENT_TYPE'];
        $headers = [];
        /**
         * @var string|int $value
         */
        foreach ($this->getArrayCopy() as $key => $value) {
            assert(is_string($key));
            if (mb_strpos($key, 'HTTP_') === 0) {
                $headers[$this->classify(mb_substr($key, 5), '-')] = $value;
            } elseif (in_array($key, $content)) {
                $headers[$this->classify($key, '-')] = $value;
            }
        }

        return $headers;
    }

    /**
     * Преобразует строку из ("string_helper" или "string.helper" или "string-helper") в "StringHelper"
     */
    private function classify(string $value, string $delimiter = ''): string
    {
        return trim(preg_replace_callback('/(^|_|\.|\-|\/)([a-z ]+)/im', function ($matches) use ($delimiter) {
            return ucfirst(mb_strtolower($matches[2])) . $delimiter;
        }, $value . ' '), ' ' . $delimiter);
    }
}

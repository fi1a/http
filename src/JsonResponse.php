<?php

declare(strict_types=1);

namespace Fi1a\Http;

use InvalidArgumentException;

use const JSON_HEX_AMP;
use const JSON_HEX_APOS;
use const JSON_HEX_QUOT;
use const JSON_HEX_TAG;

/**
 * JSON ответ
 */
class JsonResponse extends ContentResponse implements JsonResponseInterface
{
    /**
     * @var int
     */
    private $encodingOptions = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;

    /**
     * @inheritDoc
     */
    public function getEncodingOptions(): int
    {
        return $this->encodingOptions;
    }

    /**
     * @inheritDoc
     */
    public function setEncodingOptions(int $encodingOptions)
    {
        $this->encodingOptions = $encodingOptions;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function data($data = [], ?int $status = null, $headers = [])
    {
        if ($data && !is_string($data) && !is_numeric($data) && !is_array($data)) {
            throw new InvalidArgumentException('Ошибка установки значения JSON');
        }
        $this->useHeaders($headers);
        if (!is_null($status)) {
            $this->setStatus($status);
        }
        $this->setContent(json_encode($data, $this->getEncodingOptions()));

        return $this;
    }
}

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
    protected $encodingOptions = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;

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
        $object = $this->getObject();

        $object->encodingOptions = $encodingOptions;

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function data($data = [], ?int $status = null, $headers = [])
    {
        $object = $this->getObject();

        if ($data && !is_string($data) && !is_numeric($data) && !is_array($data)) {
            throw new InvalidArgumentException('Ошибка установки значения JSON');
        }
        $object = $object->useHeaders($headers);
        if (!is_null($status)) {
            $object = $object->setStatus($status);
        }
        $object = $object->setContent(json_encode($data, $this->getEncodingOptions()));

        return $object;
    }
}

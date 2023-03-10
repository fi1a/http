<?php

declare(strict_types=1);

namespace Fi1a\Http;

use InvalidArgumentException;

/**
 * Перенаправление
 */
class RedirectResponse extends Response implements RedirectResponseInterface
{
    public function __construct(
        int $status = self::HTTP_FOUND,
        ?HeaderCollectionInterface $headers = null,
        ?RequestInterface $request = null
    ) {
        parent::__construct($status, $headers, $request);
    }

    /**
     * @inheritDoc
     */
    public function to($location, ?int $status = null, $headers = [])
    {
        $object = $this->getObject();

        if (!$location) {
            throw new InvalidArgumentException('Адрес перенаправления не может быть пустым');
        }
        if ($location instanceof UriInterface) {
            $location = $location->uri();
        }
        $object = $object->useHeaders($headers)
            ->withoutHeader('Location')
            ->withHeader('Location', $location);
        if (!is_null($status)) {
            $object = $object->withStatus($status);
        }

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function getLocation(): ?UriInterface
    {
        $header = $this->headers()->getLastHeader('Location');
        if (!$header || !$header->getValue()) {
            return null;
        }

        return new Uri((string) $header->getValue());
    }

    /**
     * @inheritDoc
     */
    public function withStatus(int $status, ?string $reasonPhrase = null)
    {
        $object = parent::withStatus($status, $reasonPhrase);
        if (!$object->isRedirection()) {
            throw new InvalidArgumentException('Ошибка в статусе перенаправления');
        }

        return $object;
    }
}

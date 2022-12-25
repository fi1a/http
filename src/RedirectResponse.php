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
        if (!$location) {
            throw new InvalidArgumentException('Адрес перенаправления не может быть пустым');
        }
        if ($location instanceof UriInterface) {
            $location = $location->getUri();
        }
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
        $this->withoutHeader('Location');
        $this->withHeader('Location', $location);
        if (!is_null($status)) {
            $this->setStatus($status);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLocation(): ?UriInterface
    {
        $header = $this->getHeaders()->getLastHeader('Location');
        if (!$header || !$header->getValue()) {
            return null;
        }

        return new Uri((string) $header->getValue());
    }

    /**
     * @inheritDoc
     */
    public function setStatus(int $status, ?string $reasonPhrase = null)
    {
        parent::setStatus($status, $reasonPhrase);
        if (!$this->isRedirection()) {
            throw new InvalidArgumentException('Ошибка в статусе перенаправления');
        }

        return $this;
    }
}

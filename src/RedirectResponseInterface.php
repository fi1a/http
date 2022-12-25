<?php

declare(strict_types=1);

namespace Fi1a\Http;

/**
 * Перенаправление
 */
interface RedirectResponseInterface extends ResponseInterface
{
    /**
     * Перенаправление на адрес
     *
     * @param string|UriInterface $location
     * @param HeaderCollectionInterface|string[]|string[][] $headers
     *
     * @return $this
     */
    public function to($location, ?int $status = null, $headers = []);

    /**
     * Возвращает адрес перенаправления
     */
    public function getLocation(): ?UriInterface;
}

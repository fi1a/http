<?php

declare(strict_types=1);

namespace Fi1a\Http\Middlewares;

use Fi1a\Collection\InstanceCollectionInterface;
use Fi1a\Http\RequestInterface;
use Fi1a\Http\ResponseInterface;

/**
 * Коллекция промежуточного ПО
 *
 * @method void handleRequest(RequestInterface $request)
 * @method void handleResponse(RequestInterface $request, ResponseInterface $response)
 */
interface MiddlewareCollectionInterface extends InstanceCollectionInterface
{
    /**
     * Сортирует промежуточное ПО
     */
    public function sortByField(): MiddlewareCollectionInterface;
}

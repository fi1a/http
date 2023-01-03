<?php

declare(strict_types=1);

namespace Fi1a\Http\Middlewares;

use Fi1a\Collection\AbstractInstanceCollection;
use LogicException;

/**
 * Коллекция промежуточного ПО
 */
class MiddlewareCollection extends AbstractInstanceCollection implements MiddlewareCollectionInterface
{
    /**
     * @inheritDoc
     */
    protected function factory($key, $value)
    {
        throw new LogicException('Не поддерживается создание промежуточного ПО в коллекции');
    }

    /**
     * @inheritDoc
     */
    protected function isInstance($value): bool
    {
        return $value instanceof MiddlewareInterface;
    }

    /**
     * @inheritDoc
     */
    public function sortDirect(): MiddlewareCollectionInterface
    {
        /**
         * @var MiddlewareInterface[] $middlewares
         */
        $middlewares = $this->getArrayCopy();
        usort(
            $middlewares,
            function (MiddlewareInterface $middlewareA, MiddlewareInterface $middlewareB): int {
                return $middlewareA->getSort() - $middlewareB->getSort();
            }
        );

        return new MiddlewareCollection($middlewares);
    }

    /**
     * @inheritDoc
     */
    public function sortBack(): MiddlewareCollectionInterface
    {
        /**
         * @var MiddlewareInterface[] $middlewares
         */
        $middlewares = $this->getArrayCopy();
        usort(
            $middlewares,
            function (MiddlewareInterface $middlewareA, MiddlewareInterface $middlewareB): int {
                return $middlewareB->getSort() - $middlewareA->getSort();
            }
        );

        return new MiddlewareCollection($middlewares);
    }
}

<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http\Middlewares;

use Fi1a\Http\Middlewares\MiddlewareCollection;
use Fi1a\Unit\Http\Fixtures\Middleware;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * Коллекция промежуточного ПО
 */
class MiddlewareCollectionTest extends TestCase
{
    /**
     * Тестирование коллекции
     */
    public function testCollection(): void
    {
        $collection = new MiddlewareCollection();
        $collection[] = new Middleware();
        $collection[] = new Middleware();
        $this->assertCount(2, $collection);
    }

    /**
     * Тестирование коллекции
     */
    public function testCollectionException(): void
    {
        $this->expectException(LogicException::class);
        $collection = new MiddlewareCollection();
        $collection[] = 'value';
    }

    /**
     * Тестирование сортировки
     */
    public function testSortDirect(): void
    {
        $collection = new MiddlewareCollection();
        $collection[] = (new Middleware())->setSort(600);
        $collection[] = (new Middleware())->setSort(200);
        $newCollection = $collection->sortDirect();
        $this->assertCount(2, $newCollection);
        $this->assertEquals(200, $newCollection[0]->getSort());
        $this->assertEquals(600, $newCollection[1]->getSort());
    }

    /**
     * Тестирование сортировки
     */
    public function testSortBack(): void
    {
        $collection = new MiddlewareCollection();
        $collection[] = (new Middleware())->setSort(200);
        $collection[] = (new Middleware())->setSort(600);
        $newCollection = $collection->sortBack();
        $this->assertCount(2, $newCollection);
        $this->assertEquals(600, $newCollection[0]->getSort());
        $this->assertEquals(200, $newCollection[1]->getSort());
    }
}

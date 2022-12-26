<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use ErrorException;
use Fi1a\Http\Flush;
use PHPUnit\Framework\TestCase;

/**
 * Сохраняет значение в сессии. После получения значения, стирает его
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class FlushTest extends TestCase
{
    /**
     * Тестирование установки значения и возврата его
     */
    public function testFlush()
    {
        $session = session();
        $session->open();
        $flush = new Flush($session);
        $this->assertNull($flush->get('key'));
        $flush->set('key', 1);
        $this->assertEquals(1, $flush->get('key'));
        $this->assertNull($flush->get('key'));
    }

    /**
     * Исключение при установке значения
     */
    public function testSetException()
    {
        $this->expectException(ErrorException::class);
        $flush = new Flush();
        $flush->set('key', 1);
    }

    /**
     * Исключение при возврате значения
     */
    public function testGetException()
    {
        $this->expectException(ErrorException::class);
        $flush = new Flush();
        $flush->get('key');
    }
}

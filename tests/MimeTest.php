<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use Fi1a\Http\Mime;
use Fi1a\Http\MimeInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Mime
 */
class MimeTest extends TestCase
{
    /**
     * Возвращает mime
     */
    public function testGetMime(): void
    {
        $this->assertEquals(MimeInterface::JSON, Mime::getMime('json'));
        $this->assertEquals('unknown', Mime::getMime('unknown'));
    }

    /**
     * Возвращает mime
     */
    public function testException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Mime::getMime('');
    }
}

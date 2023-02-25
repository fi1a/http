<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http\Session;

use Fi1a\Collection\DataType\PathAccessInterface;
use Fi1a\Http\Session\SessionStorage;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Хранение сессии
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SessionStorageTest extends TestCase
{
    /**
     * Возвращает экземпляр класса хранения сессии
     */
    private function getSession(): SessionStorage
    {
        return new SessionStorage();
    }

    /**
     * Открытие
     */
    public function testOpen(): void
    {
        $session = $this->getSession();
        $this->assertTrue($session->open());
        $this->assertTrue($session->open());
        $this->assertTrue($session->close());
    }

    /**
     * Сессия уже открыта
     */
    public function testOpenByPhp(): void
    {
        session_start();
        $session = $this->getSession();
        $this->assertTrue($session->open());
        $this->assertTrue($session->open());
        $this->assertTrue($session->close());
    }

    /**
     * Исключение когда заголовки уже отправлены
     */
    public function testOpenHeadersSentException(): void
    {
        $this->expectException(RuntimeException::class);
        $session = $this->getMockBuilder(SessionStorage::class)
            ->onlyMethods(['headersSent'])
            ->getMock();

        $session->method('headersSent')->willReturn(true);
        $session->open();
    }

    /**
     * Исключение когда заголовки уже отправлены
     */
    public function testOpenSessionStartException(): void
    {
        $this->expectException(RuntimeException::class);
        $session = $this->getMockBuilder(SessionStorage::class)
            ->onlyMethods(['sessionStart'])
            ->getMock();

        $session->method('sessionStart')->willReturn(false);
        $session->open();
    }

    /**
     * Тестироване идентификатора сессии
     */
    public function testSetGetId(): void
    {
        $session = $this->getSession();
        $session->setId('123');
        $this->assertEquals('123', $session->getId());
    }

    /**
     * Тестироване имени сессии
     */
    public function testSetGetName(): void
    {
        $session = $this->getSession();
        $session->setName('som_name');
        $this->assertEquals('som_name', $session->getName());
    }

    /**
     * Регенерирует идентификатор
     */
    public function testRegenerate(): void
    {
        $session = $this->getSession();
        $this->assertFalse($session->regenerate());
        $this->assertTrue($session->open());
        $this->assertTrue($session->regenerate());
    }

    /**
     * Исключение при регенерации идентификатора
     */
    public function testRegenerateException(): void
    {
        $session = $this->getMockBuilder(SessionStorage::class)
            ->onlyMethods(['sessionStatus'])
            ->getMock();

        $session->method('sessionStatus')->willReturn(0);
        $this->assertTrue($session->open());
        $session->getValues()->set('regenerate', true);
        $this->assertTrue($session->getValues()->has('regenerate'));
        $session->regenerate();
        $this->assertFalse($session->getValues()->has('regenerate'));
    }

    /**
     * Очищает сессию
     */
    public function testClear(): void
    {
        $session = $this->getSession();
        $this->assertFalse($session->getValues());
        $this->assertFalse($session->clear());
        $this->assertTrue($session->open());
        $this->assertInstanceOf(PathAccessInterface::class, $session->getValues());
        $session->getValues()->set('fixture:key1', 1);
        $this->assertEquals(1, $session->getValues()->get('fixture:key1'));
        $this->assertTrue($session->clear());
        $this->assertNull($session->getValues()->get('fixture:key1'));
    }

    /**
     * Идентификатор и имя
     */
    public function testIdAndName(): void
    {
        $session = $this->getSession();
        $this->assertEquals('', $session->getId());
        $this->assertTrue($session->open());
        $this->assertIsString($session->getId());
        $this->assertIsString($session->getName());
        $this->assertNotSame('', $session->getId());
        $this->assertNotSame('', $session->getName());
        $this->assertTrue($session->close());
        $id = 'fixtureid';
        $name = 'fixturename';
        $session->setId($id);
        $session->setName($name);
        $this->assertEquals($id, $session->getId());
        $this->assertEquals($name, $session->getName());
    }

    /**
     * Значения в разных экземплярах
     */
    public function testSync(): void
    {
        $session1 = $this->getSession();
        $session2 = $this->getSession();
        $session1->open();
        $session2->open();
        $this->assertFalse($session1->getValues()->has('fixture:sync'));
        $this->assertFalse($session2->getValues()->has('fixture:sync'));
        $session1->getValues()->set('fixture:sync', 1);
        $this->assertTrue($session1->getValues()->has('fixture:sync'));
        $this->assertTrue($session2->getValues()->has('fixture:sync'));
    }
}

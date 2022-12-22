<?php

declare(strict_types=1);

namespace Fi1a\Unit\Http;

use Fi1a\Http\UploadFile;
use Fi1a\Http\UploadFileInterface;
use PHPUnit\Framework\TestCase;

/**
 * Файл
 */
class UploadFileTest extends TestCase
{
    /**
     * Возвращает файл
     */
    private function getUploadFile(): UploadFileInterface
    {
        return new UploadFile([
            'error' => 0,
            'name' => 'filename.txt',
            'type' => 'txt',
            'tmp_name' => '/tmp/filename',
            'size' => 100,
        ]);
    }

    /**
     * Ошибка
     */
    public function testError(): void
    {
        $uploadFile = $this->getUploadFile();
        $this->assertEquals(0, $uploadFile->getError());
        $uploadFile->setError(1);
        $this->assertEquals(1, $uploadFile->getError());
    }

    /**
     * Имя
     */
    public function testName(): void
    {
        $uploadFile = $this->getUploadFile();
        $this->assertEquals('filename.txt', $uploadFile->getName());
        $uploadFile->setName('filename2.txt');
        $this->assertEquals('filename2.txt', $uploadFile->getName());
    }

    /**
     * Тип
     */
    public function testType(): void
    {
        $uploadFile = $this->getUploadFile();
        $this->assertEquals('txt', $uploadFile->getType());
        $uploadFile->setType('zip');
        $this->assertEquals('zip', $uploadFile->getType());
    }

    /**
     * Путь
     */
    public function testTmpName(): void
    {
        $uploadFile = $this->getUploadFile();
        $this->assertEquals('/tmp/filename', $uploadFile->getTmpName());
        $uploadFile->setTmpName('/tmp/filename2');
        $this->assertEquals('/tmp/filename2', $uploadFile->getTmpName());
    }

    /**
     * Размер
     */
    public function testSize(): void
    {
        $uploadFile = $this->getUploadFile();
        $this->assertEquals(100, $uploadFile->getSize());
        $uploadFile->setSize(200);
        $this->assertEquals(200, $uploadFile->getSize());
    }
}

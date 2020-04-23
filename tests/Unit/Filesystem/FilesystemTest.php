<?php

declare(strict_types=1);

namespace Unit\Filesystem;

use League\Flysystem\FilesystemInterface;
use Ntavelis\Dockposer\Exception\FileNotFoundException;
use Ntavelis\Dockposer\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;

class FilesystemTest extends TestCase
{
    /**
     * @var Filesystem;
     */
    private $filesystem;

    public function setUp(): void
    {
        parent::setUp();

        $this->filesystem = new Filesystem($this->createMock(FilesystemInterface::class));
    }

    /** @test */
    public function itCanCompileAStub(): void
    {
        $contents = $this->filesystem->compileStub(__DIR__ . '/test.stub');

        $this->assertSame('<3 Dockposer', $contents);
    }

    /** @test */
    public function ifTheStubDoesNotExistWeThrowAnException(): void
    {
        $this->expectException(FileNotFoundException::class);

        $this->filesystem->compileStub('not-existent.stub');
    }
}

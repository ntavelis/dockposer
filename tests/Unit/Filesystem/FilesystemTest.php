<?php

declare(strict_types=1);

namespace Unit\Filesystem;

use Ntavelis\Dockposer\Exception\FileNotFoundException;
use Ntavelis\Dockposer\Exception\UnableToCreateDirectory;
use Ntavelis\Dockposer\Exception\UnableToPutContentsToFile;
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

        $this->filesystem = new Filesystem(__DIR__);
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

    /** @test */
    public function itCanCreateAFileWithGivenContents()
    {
        $file = 'test.txt';
        $this->filesystem->put($file, 'Make sure it works!');

        $this->assertFileExists(__DIR__ . '/' . $file);
        $this->assertFileIsReadable(__DIR__ . '/' . $file);
        $this->assertFileIsWritable(__DIR__ . '/' . $file);
        $this->assertStringEqualsFile(__DIR__ . '/' . $file, 'Make sure it works!');

        $this->filesystem->put($file, 'Now I changed completely!');

        $this->assertStringEqualsFile(__DIR__ . '/' . $file, 'Now I changed completely!');

        // cleanup
        exec('rm ' . __DIR__ . '/' . $file);
    }

    /** @test */
    public function ifWeCanNotUpdateTheContentsOfAGivenFileWeThrowException()
    {
        $this->expectException(UnableToPutContentsToFile::class);

        $this->filesystem->put('./not-existent-dir/test.txt', 'Now I changed completely!');

        $this->assertFileNotExists('/not-existent-dir/test.txt');
    }

    /** @test */
    public function itCanCreateADirectory()
    {
        $this->filesystem->createDir('test');

        $this->assertDirectoryExists(__DIR__ . '/test');

        // cleanup
        exec('rm -rf ' . __DIR__ . '/test');
    }

    /** @test */
    public function ifWeCanNotCreateADirectoryWeThrowAnException()
    {
        $this->expectException(UnableToCreateDirectory::class);
        $this->filesystem->createDir('test/nested/dir');
    }

}

<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Tests\Unit\Filesystem;

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
    public function theCompileStubPathNeedsAnAbsoluteUrlToBePassed(): void
    {
        $this->expectException(FileNotFoundException::class);
        $this->filesystem->compileStub('/relative/url/to/test.stub');
    }

    /** @test */
    public function ifTheStubDoesNotExistWeThrowAnException(): void
    {
        $this->expectException(FileNotFoundException::class);

        $this->filesystem->compileStub('not-existent.stub');
    }

    /** @test */
    public function itCanCreateAFileWithGivenContents(): void
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
    public function ifWeCanNotUpdateTheContentsOfAGivenFileWeThrowException(): void
    {
        $this->expectException(UnableToPutContentsToFile::class);

        $this->filesystem->put('./not-existent-dir/test.txt', 'dummy text');

        $this->assertFileNotExists('/not-existent-dir/test.txt');
    }

    /** @test */
    public function itCanCreateADirectory(): void
    {
        $this->filesystem->createDir('test');

        $this->assertDirectoryExists(__DIR__ . '/test');

        // cleanup
        exec('rm -rf ' . __DIR__ . '/test');
    }

    /** @test */
    public function ifWeCanNotCreateADirectoryWeThrowAnException(): void
    {
        $this->expectException(UnableToCreateDirectory::class);
        $this->filesystem->createDir('test/nested/dir');
    }

    /** @test */
    public function itCanCheckIfAFileExists(): void
    {
        $this->assertTrue($this->filesystem->fileExists('FilesystemTest.php'));
        $this->assertFalse($this->filesystem->fileExists('NotExistent.php'));
    }

    /** @test */
    public function itCanCheckIfADirExists(): void
    {
        $this->assertTrue($this->filesystem->dirExists('../Filesystem'));
        $this->assertFalse($this->filesystem->dirExists('not_a_dir'));
    }

    /** @test */
    public function itCanReadTheContentsOfAFile(): void
    {
        $contents = $this->filesystem->readFile('test.stub');

        $this->assertSame('<3 Dockposer', $contents);
    }

    /** @test */
    public function itWillThrowAnExceptionIfItCanNotReadAFile(): void
    {
        $this->expectException(FileNotFoundException::class);
        $this->filesystem->readFile('not-existent.stub');
    }
}

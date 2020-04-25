<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Tests\Unit\Executors;

use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\DockposerConfig;
use Ntavelis\Dockposer\Enum\ExecutorStatus;
use Ntavelis\Dockposer\Exception\UnableToCreateDirectory;
use Ntavelis\Dockposer\Executors\DockerDirectoryExecutor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DockerDirectoryExecutorTest extends TestCase
{
    /**
     * @var DockerDirectoryExecutor
     */
    private $executor;
    /**
     * @var FilesystemInterface|MockObject
     */
    private $filesystem;

    public function setUp(): void
    {
        parent::setUp();

        $this->filesystem = $this->createMock(FilesystemInterface::class);
        $config = new DockposerConfig(__DIR__, __DIR__ . '/demoapp');
        $this->executor = new DockerDirectoryExecutor($this->filesystem, $config);
    }

    /** @test */
    public function itWillCreateADirectoryToHoldTheDockerFiles(): void
    {
        $result = $this->executor->execute();

        $this->assertSame('Created docker directory, at ./docker', $result->getResult());
        $this->assertSame(ExecutorStatus::SUCCESS, $result->getStatus());
    }

    /** @test */
    public function ifTheDirectoryDoesNotExistItWillReturnTrueWhenAskedIfItShouldBeExecuted(): void
    {
        $bool = $this->executor->shouldExecute();

        $this->assertTrue($bool);
    }

    /** @test */
    public function ifTheDirectoryDoesExistItWillReturnFalseWhenAskedIfItShouldBeExecuted(): void
    {
        // Give it a directory that exists
        $config = new DockposerConfig(__DIR__, dirname(__DIR__), [
            'docker_dir' => 'Executors',
        ]);
        $executor = new DockerDirectoryExecutor($this->filesystem, $config);
        $this->filesystem->expects($this->once())->method('dirExists')->willReturn(true);

        $bool = $executor->shouldExecute();

        $this->assertFalse($bool);
    }

    /** @test */
    public function ifThereIsAFilesystemErrorWeAbortWithAppropriateMessage(): void
    {
        $this->filesystem->expects($this->once())->method('createDir')->willThrowException(new UnableToCreateDirectory('directory already exists'));

        $result = $this->executor->execute();

        $this->assertSame('Unable to create docker directory, reason: directory already exists', $result->getResult());
        $this->assertSame(ExecutorStatus::FAIL, $result->getStatus());
    }
}

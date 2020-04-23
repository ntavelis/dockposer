<?php

declare(strict_types=1);

namespace Unit\Executors;

use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\DockposerConfig;
use Ntavelis\Dockposer\Enum\ExecutorStatus;
use Ntavelis\Dockposer\Executors\DockerComposeExecutor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DockerComposeExecutorTest extends TestCase
{
    /**
     * @var DockerComposeExecutor
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
        $this->executor = new DockerComposeExecutor($this->filesystem, $config);
    }

    /** @test */
    public function itWillCreateADockerComposeFile(): void
    {
        $result = $this->executor->execute();

        $this->assertSame('Added docker-compose file, at ./docker-compose.yml', $result->getResult());
        $this->assertSame(ExecutorStatus::SUCCESS, $result->getStatus());
    }

    /** @test */
    public function ifThFileDoesNotExistItWillReturnTrueWhenAskedIfItShouldBeExecuted(): void
    {
        $bool = $this->executor->shouldExecute();

        $this->assertTrue($bool);
    }

    /** @test */
    public function ifTheFileDoesExistItWillReturnFalseWhenAskedIfItShouldBeExecuted(): void
    {
        // Give it a file that it exists
        $config = new DockposerConfig(__DIR__, dirname(__DIR__), [
            'docker_dir' => 'Executors',
            'docker_compose_file' => 'Executors/DockerComposeExecutorTest.php',
        ]);
        $executor = new DockerComposeExecutor($this->filesystem, $config);

        $bool = $executor->shouldExecute();

        $this->assertFalse($bool);
    }
}

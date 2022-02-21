<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Tests\Unit\Executors;

use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\DockposerConfig;
use Ntavelis\Dockposer\Enum\ExecutorStatus;
use Ntavelis\Dockposer\Exception\FileNotFoundException;
use Ntavelis\Dockposer\Executors\DockerComposeExecutor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DockerComposeExecutorTest extends TestCase
{
    private DockerComposeExecutor $executor;
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
        $this->filesystem->expects($this->once())->method('compileStub');
        $this->filesystem->expects($this->once())->method('put');
        $result = $this->executor->execute();

        $this->assertSame('Added docker-compose file, at ./docker-compose.yml', $result->getResult());
        $this->assertSame(ExecutorStatus::SUCCESS, $result->getStatus());
    }

    /** @test */
    public function itWillReplaceAllTheDynamicVariablesWithValuesFromConfiguration(): void
    {
        $this->filesystem->expects($this->once())->method('compileStub')->willReturn('{{docker_dir}} {{fpm_docker_dir}} {{nginx_docker_dir}} {{dockerfile_name}}');
        // Indicates that the value, has been replaced e.g with {{docker_dir}}
        $this->filesystem->expects($this->once())->method('put')->with('docker-compose.yml', 'docker php-fpm nginx Dockerfile');
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
        $this->filesystem->expects($this->once())->method('fileExists')->willReturn(true);

        $bool = $executor->shouldExecute();

        $this->assertFalse($bool);
    }

    /** @test */
    public function ifThereIsAFilesystemErrorWeAbortWithAppropriateMessage(): void
    {
        $this->filesystem->expects($this->once())->method('compileStub')->willThrowException(new FileNotFoundException('can not read file'));

        $result = $this->executor->execute();

        $this->assertSame('Unable to create docker-compose.yml file, reason: can not read file', $result->getResult());
        $this->assertSame(ExecutorStatus::FAIL, $result->getStatus());
    }
}

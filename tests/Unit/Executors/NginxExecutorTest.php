<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Tests\Unit\Executors;

use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\DockposerConfig;
use Ntavelis\Dockposer\Enum\ExecutorStatus;
use Ntavelis\Dockposer\Exception\FileNotFoundException;
use Ntavelis\Dockposer\Executors\NginxExecutor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NginxExecutorTest extends TestCase
{
    /**
     * @var NginxExecutor
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
        $this->executor = new NginxExecutor($this->filesystem, $config);
    }

    /** @test */
    public function itWillCreateADockerfileForNginx(): void
    {
        $result = $this->executor->execute();

        $this->assertSame('Added nginx Dockerfile at ./docker/nginx/Dockerfile', $result->getResult());
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
        $config = new DockposerConfig(__DIR__, dirname(__DIR__, 2), [
            'docker_dir' => 'Unit',
            'nginx_docker_dir' => 'Executors',
            'dockerfile_name' => 'NginxExecutorTest.php',
        ]);
        $executor = new NginxExecutor($this->filesystem, $config);
        $this->filesystem->expects($this->once())->method('fileExists')->willReturn(true);

        $bool = $executor->shouldExecute();

        $this->assertFalse($bool);
    }

    /** @test */
    public function ifThereIsAFilesystemErrorWeAbortWithAppropriateMessage(): void
    {
        $this->filesystem->expects($this->once())->method('compileStub')->willThrowException(new FileNotFoundException('can not read file'));

        $result = $this->executor->execute();

        $this->assertSame('Unable to create nginx dockerfile, reason: can not read file', $result->getResult());
        $this->assertSame(ExecutorStatus::FAIL, $result->getStatus());
    }

    /** @test */
    public function itWillReplaceAllTheDynamicVariablesWithValuesFromConfiguration(): void
    {
        $this->filesystem->expects($this->once())->method('compileStub')->willReturn('./{{nginx_config_file}}');
        // Indicates that the value, has been replaced e.g with {{docker_dir}}
        $this->filesystem->expects($this->once())->method('put')->with('docker/nginx/Dockerfile', './docker/nginx/default.conf');
        $result = $this->executor->execute();

        $this->assertSame('Added nginx Dockerfile at ./docker/nginx/Dockerfile', $result->getResult());
        $this->assertSame(ExecutorStatus::SUCCESS, $result->getStatus());
    }
}

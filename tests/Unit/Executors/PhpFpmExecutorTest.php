<?php

declare(strict_types=1);

namespace Unit\Executors;

use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\DockposerConfig;
use Ntavelis\Dockposer\Enum\ExecutorStatus;
use Ntavelis\Dockposer\Exception\FileNotFoundException;
use Ntavelis\Dockposer\Executors\PhpFpmExecutor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PhpFpmExecutorTest extends TestCase
{
    /**
     * @var PhpFpmExecutor
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
        $this->executor = new PhpFpmExecutor($this->filesystem, $config);
    }

    /** @test */
    public function itWillCreateADockerfileForNginx(): void
    {
        $result = $this->executor->execute();

        $this->assertSame('Added php-fpm Dockerfile at ./docker/php-fpm/Dockerfile', $result->getResult());
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
            'fpm_docker_dir' => 'Executors',
            'dockerfile_name' => 'PhpFpmExecutorTest.php',
        ]);
        $executor = new PhpFpmExecutor($this->filesystem, $config);
        $this->filesystem->expects($this->once())->method('fileExists')->willReturn(true);

        $bool = $executor->shouldExecute();

        $this->assertFalse($bool);
    }

    /** @test */
    public function ifThereIsAFilesystemErrorWeAbortWithAppropriateMessage(): void
    {
        $this->filesystem->expects($this->once())->method('compileStub')->willThrowException(new FileNotFoundException('can not read file'));

        $result = $this->executor->execute();

        $this->assertSame('Unable to create php-fpm dockerfile, reason: can not read file', $result->getResult());
        $this->assertSame(ExecutorStatus::FAIL, $result->getStatus());
    }
}

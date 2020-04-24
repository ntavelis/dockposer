<?php

declare(strict_types=1);

namespace Unit\Executors;

use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\DockposerConfig;
use Ntavelis\Dockposer\Enum\ExecutorStatus;
use Ntavelis\Dockposer\Executors\PhpVersionExecutor;
use Ntavelis\Dockposer\Provider\PlatformDependenciesProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PhpVersionExecutorTest extends TestCase
{
    /**
     * @var PhpVersionExecutor
     */
    private $executor;
    /**
     * @var FilesystemInterface|MockObject
     */
    private $filesystem;
    /**
     * @var array
     */
    private $composerDependencies = [
        'php' => '[>= 7.2.5.0-dev < 8.0.0.0-dev]',
        'ext-ctype' => '[]',
        'ext-iconv' => '[]',
        'ntavelis/dockposer' => '== 9999999-dev',
    ];
    /**
     * @var PlatformDependenciesProvider
     */
    private $provider;
    /**
     * @var DockposerConfig
     */
    private $config;

    public function setUp(): void
    {
        parent::setUp();

        $this->filesystem = $this->createMock(FilesystemInterface::class);
        $this->config = new DockposerConfig(__DIR__, __DIR__ . '/demoapp');
        $this->provider = new PlatformDependenciesProvider($this->composerDependencies);

        $this->executor = new PhpVersionExecutor($this->filesystem, $this->config, $this->provider);
    }

    /** @test */
    public function ifThFileDoesExistItWillReturnTrueWhenAskedIfItShouldBeExecuted(): void
    {
        $this->filesystem->expects($this->once())->method('fileExists')->willReturn(true);
        $bool = $this->executor->shouldExecute();

        $this->assertTrue($bool);
    }

    /** @test */
    public function itWillUpdateThePhpVersionInThePhpFpmDockerFile(): void
    {
        $this->filesystem
            ->expects($this->once())
            ->method('readFile')
            ->willReturn("###> ntavelis/dockposer/php-docker-image ###\nFROM php:{{php_version}}-fpm\n###> ntavelis/dockposer/php-docker-image ###");
        $this->filesystem
            ->expects($this->once())
            ->method('put')
            ->with('docker/php-fpm/Dockerfile', "###> ntavelis/dockposer/php-docker-image ###\nFROM php:7.2-fpm\n###> ntavelis/dockposer/php-docker-image ###");

        $this->executor->execute();
    }

    /** @test */
    public function itWillUpdateThePhpVersionInThePhpFpmDockerFileInCaseItWasUpdatedInComposerJsonFile(): void
    {
        $provider = new PlatformDependenciesProvider([
            'php' => '[>= 7.3.0.0-dev < 8.0.0.0-dev]',
            'ext-ctype' => '[]',
            'ext-iconv' => '[]',
            'ntavelis/dockposer' => '== 9999999-dev',
        ]);
        $executor = new PhpVersionExecutor($this->filesystem, $this->config, $provider);
        $this->filesystem
            ->expects($this->once())
            ->method('readFile')
            ->willReturn("###> ntavelis/dockposer/php-docker-image ###\nFROM php:7.2-fpm\n###> ntavelis/dockposer/php-docker-image ###");
        $this->filesystem
            ->expects($this->once())
            ->method('put')
            ->with('docker/php-fpm/Dockerfile', "###> ntavelis/dockposer/php-docker-image ###\nFROM php:7.3-fpm\n###> ntavelis/dockposer/php-docker-image ###");

        $executor->execute();
    }

    /** @test */
    public function itWillNotUpdateThePhpVersionInThePhpFpmDockerFileInCaseItResolvesToTheSameMajorVersionAsTheOneItCurrentlyHas(): void
    {
        $this->filesystem
            ->expects($this->once())
            ->method('readFile')
            ->willReturn("###> ntavelis/dockposer/php-docker-image ###\nFROM php:7.2-fpm\n###> ntavelis/dockposer/php-docker-image ###");
        $this->filesystem->expects($this->never())->method('put');

        $result = $this->executor->execute();
        $this->assertSame(ExecutorStatus::SKIPPED, $result->getStatus());
    }
}

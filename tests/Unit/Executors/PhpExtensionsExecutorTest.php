<?php

declare(strict_types=1);

namespace Unit\Executors;

use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\DockposerConfig;
use Ntavelis\Dockposer\Executors\PhpExtensionsExecutor;
use Ntavelis\Dockposer\Provider\PlatformDependenciesProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PhpExtensionsExecutorTest extends TestCase
{
    /**
     * @var PhpExtensionsExecutor
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
        'ext-bcmath' => '[]',
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

        $this->executor = new PhpExtensionsExecutor($this->filesystem, $this->config, $this->provider);
    }

    /** @test */
    public function ifThFileDoesExistItWillReturnTrueWhenAskedIfItShouldBeExecuted(): void
    {
        $this->filesystem->expects($this->once())->method('fileExists')->willReturn(true);
        $bool = $this->executor->shouldExecute();

        $this->assertTrue($bool);
    }

    /** @test */
    public function itWillUpdateThePhpExtensionsListInThePhpFpmDockerFile(): void
    {
        $this->filesystem
            ->expects($this->once())
            ->method('readFile')
            ->willReturn(
                "###> ntavelis/dockposer/php-extensions ###\n" .
                "COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/\n" .
                "RUN install-php-extensions \nsoap\n" .
                "###> ntavelis/dockposer/php-extensions ###"
            );
        $this->filesystem
            ->expects($this->once())
            ->method('put')
            ->with(
                'docker/php-fpm/Dockerfile',
                "###> ntavelis/dockposer/php-extensions ###\n" .
                "COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/\n" .
                "RUN install-php-extensions \nbcmath\n" .
                "###> ntavelis/dockposer/php-extensions ###"
            );

        $this->executor->execute();
    }
}

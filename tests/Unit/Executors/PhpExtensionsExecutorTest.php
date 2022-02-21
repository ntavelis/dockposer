<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Tests\Unit\Executors;

use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\DockposerConfig;
use Ntavelis\Dockposer\Enum\ExecutorStatus;
use Ntavelis\Dockposer\Exception\FileNotFoundException;
use Ntavelis\Dockposer\Executors\PhpExtensionsExecutor;
use Ntavelis\Dockposer\Provider\PlatformDependenciesProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PhpExtensionsExecutorTest extends TestCase
{
    private PhpExtensionsExecutor $executor;
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
                "RUN install-php-extensions \\\n\t" .
                "soap\n" .
                "###> ntavelis/dockposer/php-extensions ###"
            );
        $this->filesystem
            ->expects($this->once())
            ->method('put')
            ->with(
                'docker/php-fpm/Dockerfile',
                "###> ntavelis/dockposer/php-extensions ###\n" .
                "COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/\n" .
                "RUN install-php-extensions \\\n\t" .
                "bcmath\n" .
                "###> ntavelis/dockposer/php-extensions ###"
            );

        $result = $this->executor->execute();
        $this->assertSame('Replaced php extensions in php-fpm dockerfile ./docker/php-fpm/Dockerfile', $result->getResult());
        $this->assertSame(ExecutorStatus::SUCCESS, $result->getStatus());
    }

    /** @test */
    public function itWillUpdateMultiplePhpExtensionsInTheListInThePhpFpmDockerFile(): void
    {
        $composerDependencies = [
            'php' => '[>= 7.2.5.0-dev < 8.0.0.0-dev]',
            'ext-amqp' => '[]',
            'ext-bcmath' => '[]',
            'ext-redis' => '[]',
            'ntavelis/dockposer' => '== 9999999-dev',
        ];
        $provider = new PlatformDependenciesProvider($composerDependencies);
        $executor = new PhpExtensionsExecutor($this->filesystem, $this->config, $provider);
        $this->filesystem
            ->expects($this->once())
            ->method('readFile')
            ->willReturn(
                "###> ntavelis/dockposer/php-extensions ###\n" .
                "COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/\n" .
                "RUN install-php-extensions \\\n\t" .
                "soap\n" .
                "###> ntavelis/dockposer/php-extensions ###"
            );
        $this->filesystem
            ->expects($this->once())
            ->method('put')
            ->with(
                'docker/php-fpm/Dockerfile',
                "###> ntavelis/dockposer/php-extensions ###\n" .
                "COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/\n" .
                "RUN install-php-extensions \\\n\t" .
                "amqp \\\n\t" .
                "bcmath \\\n\t" .
                "redis\n" .
                "###> ntavelis/dockposer/php-extensions ###"
            );

        $result = $executor->execute();
        $this->assertSame(ExecutorStatus::SUCCESS, $result->getStatus());
    }

    /** @test */
    public function itWillSortTheExternalDependenciesInThePhpFpmDockerFile(): void
    {
        $composerDependencies = [
            'php' => '[>= 7.2.5.0-dev < 8.0.0.0-dev]',
            'ext-bcmath' => '[]',
            'ext-redis' => '[]',
            'ext-amqp' => '[]',
            'ntavelis/dockposer' => '== 9999999-dev',
        ];
        $provider = new PlatformDependenciesProvider($composerDependencies);
        $executor = new PhpExtensionsExecutor($this->filesystem, $this->config, $provider);
        $this->filesystem
            ->expects($this->once())
            ->method('readFile')
            ->willReturn(
                "###> ntavelis/dockposer/php-extensions ###\n" .
                "COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/\n" .
                "RUN install-php-extensions \\\n\t" .
                "soap\n" .
                "###> ntavelis/dockposer/php-extensions ###"
            );
        $this->filesystem
            ->expects($this->once())
            ->method('put')
            ->with(
                'docker/php-fpm/Dockerfile',
                "###> ntavelis/dockposer/php-extensions ###\n" .
                "COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/\n" .
                "RUN install-php-extensions \\\n\t" .
                "amqp \\\n\t" .
                "bcmath \\\n\t" .
                "redis\n" .
                "###> ntavelis/dockposer/php-extensions ###"
            );

        $result = $executor->execute();
        $this->assertSame(ExecutorStatus::SUCCESS, $result->getStatus());
    }

    /** @test */
    public function itWillNotUpdateTheDependenciesSectionInCaseItResolvesToTheSameListOfDependencies(): void
    {
        $this->filesystem
            ->expects($this->once())
            ->method('readFile')
            ->willReturn(
                "###> ntavelis/dockposer/php-extensions ###\n" .
                "COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/\n" .
                "RUN install-php-extensions \\\n\t" .
                "bcmath\n" .
                "###> ntavelis/dockposer/php-extensions ###"
            );

        $result = $this->executor->execute();
        $this->assertSame('Nothing to update', $result->getResult());
        $this->assertSame(ExecutorStatus::SKIPPED, $result->getStatus());
    }

    /** @test */
    public function ifTheFileIsNotMarkedWeDoNotPerformAnyActionsAndReturnAppropriateResult(): void
    {
        $this->filesystem
            ->expects($this->once())
            ->method('readFile')
            ->willReturn("other content not marked file");

        $result = $this->executor->execute();

        $this->assertSame('file not marked', $result->getResult());
        $this->assertSame(ExecutorStatus::NOT_MARKED, $result->getStatus());
    }

    /** @test */
    public function ifTheNumberOfExtensionsIsZeroWeAddAppropriateMessageAndRemoveTheExtensionCommands(): void
    {
        $composerDependencies = [
            'php' => '[>= 7.2.5.0-dev < 8.0.0.0-dev]',
            'ntavelis/dockposer' => '== 9999999-dev',
        ];
        $provider = new PlatformDependenciesProvider($composerDependencies);
        $executor = new PhpExtensionsExecutor($this->filesystem, $this->config, $provider);
        $this->filesystem
            ->expects($this->once())
            ->method('readFile')
            ->willReturn(
                "###> ntavelis/dockposer/php-extensions ###\n" .
                "COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/\n" .
                "RUN install-php-extensions \\\n\t" .
                "soap\n" .
                "###> ntavelis/dockposer/php-extensions ###"
            );
        $this->filesystem
            ->expects($this->once())
            ->method('put')
            ->with(
                'docker/php-fpm/Dockerfile',
                "###> ntavelis/dockposer/php-extensions ###\n" .
                "# You have installed all the required extensions, or you are requiring prebuild extensions that already exist inside the image\n" .
                "###> ntavelis/dockposer/php-extensions ###"
            );

        $result = $executor->execute();
        $this->assertSame(ExecutorStatus::SUCCESS, $result->getStatus());
    }

    /** @test */
    public function ifThereIsAFilesystemErrorWeAbortWithAppropriateMessage(): void
    {
        $this->filesystem->expects($this->once())->method('readFile')->willThrowException(new FileNotFoundException('can not read file'));

        $result = $this->executor->execute();

        $this->assertSame('Unable to replace php extensions in php-fpm docker file, reason: can not read file', $result->getResult());
        $this->assertSame(ExecutorStatus::FAIL, $result->getStatus());
    }
}

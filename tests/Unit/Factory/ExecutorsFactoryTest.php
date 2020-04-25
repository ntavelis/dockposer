<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Tests\Unit\Factory;

use Ntavelis\Dockposer\Contracts\ExecutorInterface;
use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\DockposerConfig;
use Ntavelis\Dockposer\Factory\ExecutorsFactory;
use Ntavelis\Dockposer\Provider\PlatformDependenciesProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ExecutorsFactoryTest extends TestCase
{
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
    /**
     * @var ExecutorsFactory
     */
    private $factory;


    public function setUp(): void
    {
        parent::setUp();

        $this->filesystem = $this->createMock(FilesystemInterface::class);
        $this->config = new DockposerConfig(__DIR__, __DIR__ . '/demoapp');
        $this->provider = new PlatformDependenciesProvider($this->composerDependencies);
        $this->factory = new ExecutorsFactory($this->config, $this->filesystem, $this->provider);
    }

    /** @test */
    public function itCanGenerateAnArrayWithTheDefaultExecutors(): void
    {
        $defaultExecutorsArray = $this->factory->createDefaultExecutors();

        $this->assertContainsOnlyInstancesOf(ExecutorInterface::class, $defaultExecutorsArray);
    }
}

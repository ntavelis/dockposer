<?php

declare(strict_types=1);

namespace Unit;

use Composer\IO\IOInterface;
use Ntavelis\Dockposer\Contracts\ExecutorInterface;
use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\DockposerConfig;
use Ntavelis\Dockposer\DockposerExecutor;
use Ntavelis\Dockposer\Provider\PlatformDependenciesProvider;
use PHPUnit\Framework\TestCase;

class DockerExecutorTest extends TestCase
{
    /**
     * @var DockposerExecutor
     */
    private $executor;

    /**
     * @var array
     */
    private $composerDependencies = [
        'php' => '[>= 7.2.5.0-dev < 8.0.0.0-dev]',
        'ext-ctype' => '[]',
        'ext-iconv' => '[]',
        'ntavelis/dockposer' => '== 9999999-dev',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $config = new DockposerConfig('/srv/app/dockposer', '/srv/app/demoapp');
        $platformDependencies = new PlatformDependenciesProvider($this->composerDependencies);
        $filesystem = $this->createMock(FilesystemInterface::class);
        $io = $this->createMock(IOInterface::class);
        $this->executor = new DockposerExecutor($config, $platformDependencies, $filesystem, $io);
    }

    /** @test */
    public function itWillIterateAllTheGivenExecutorsAndGivenThatTheySupportAGivenActionWillCallThemToExecutreTheirLogic(): void
    {
        $executor1 = $this->createMock(ExecutorInterface::class);
        $executor1->expects($this->once())->method('execute');
        $executor1->expects($this->once())->method('supports')->willReturn(true);

        $this->executor->run([$executor1]);
    }
}

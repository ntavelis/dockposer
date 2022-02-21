<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Factory;

use Ntavelis\Dockposer\Contracts\ExecutorInterface;
use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\DockposerConfig;
use Ntavelis\Dockposer\Executors\DockerComposeExecutor;
use Ntavelis\Dockposer\Executors\DockerDirectoryExecutor;
use Ntavelis\Dockposer\Executors\NginxConfigExecutor;
use Ntavelis\Dockposer\Executors\NginxExecutor;
use Ntavelis\Dockposer\Executors\PhpExtensionsExecutor;
use Ntavelis\Dockposer\Executors\PhpFpmExecutor;
use Ntavelis\Dockposer\Executors\PhpVersionExecutor;
use Ntavelis\Dockposer\Provider\PlatformDependenciesProvider;

class ExecutorsFactory
{
    private DockposerConfig $config;
    private FilesystemInterface $filesystem;
    private PlatformDependenciesProvider $platformDependenciesProvider;

    public function __construct(
        DockposerConfig $config,
        FilesystemInterface $filesystem,
        PlatformDependenciesProvider $platformDependenciesProvider
    ) {
        $this->config = $config;
        $this->filesystem = $filesystem;
        $this->platformDependenciesProvider = $platformDependenciesProvider;
    }

    /**
     * @return ExecutorInterface[]
     */
    public function createDefaultExecutors(): array
    {
        return [
            new DockerComposeExecutor($this->filesystem, $this->config),
            new DockerDirectoryExecutor($this->filesystem, $this->config),
            new NginxExecutor($this->filesystem, $this->config),
            new NginxConfigExecutor($this->filesystem, $this->config),
            new PhpFpmExecutor($this->filesystem, $this->config),
            new PhpVersionExecutor($this->filesystem, $this->config, $this->platformDependenciesProvider),
            new PhpExtensionsExecutor($this->filesystem, $this->config, $this->platformDependenciesProvider),
        ];
    }
}

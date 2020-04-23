<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Factory;

use Ntavelis\Dockposer\Contracts\ExecutorInterface;
use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\DockposerConfig;
use Ntavelis\Dockposer\Executors\DockerComposeExecutor;
use Ntavelis\Dockposer\Executors\DockerDirectoryExecutor;
use Ntavelis\Dockposer\Executors\NginxExecutor;
use Ntavelis\Dockposer\Executors\PhpFpmExecutor;

class ExecutorsFactory
{
    /**
     * @var DockposerConfig
     */
    private $config;
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    public function __construct(DockposerConfig $config, FilesystemInterface $filesystem)
    {
        $this->config = $config;
        $this->filesystem = $filesystem;
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
            new PhpFpmExecutor($this->filesystem, $this->config),
        ];
    }
}

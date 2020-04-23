<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer;

use Composer\IO\IOInterface;
use Ntavelis\Dockposer\Contracts\ExecutorInterface;
use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\Filesystem\Filesystem;
use Ntavelis\Dockposer\Provider\PlatformDependenciesProvider;

class DockposerExecutor
{
    /**
     * @var DockposerConfig
     */
    private $config;
    /**
     * @var PlatformDependenciesProvider
     */
    private $platformDependenciesProvider;
    /**
     * @var FilesystemInterface
     */
    private $filesystem;
    /**
     * @var IOInterface
     */
    private $io;

    public function __construct(
        DockposerConfig $config,
        PlatformDependenciesProvider $platformDependenciesProvider,
        FilesystemInterface $filesystem,
        IOInterface $io
    ) {
        $this->config = $config;
        $this->platformDependenciesProvider = $platformDependenciesProvider;
        $this->filesystem = $filesystem;
        $this->io = $io;
    }

    /**
     * @param ExecutorInterface[] $executors
     */
    public function run(array $executors): void
    {
        foreach ($executors as $executor) {
            if($executor->supports('')){
                $executor->execute();
            }
        }
        // Create docker dir
        $this->filesystem->createDir('docker');
        // Add php pfm dockerfile
        $this->filesystem->createDir('docker/php-fpm');
        $stub = file_get_contents($this->config->getDockposerDir() . '/stubs/dockerfile-php-fpm.stub');
        $this->filesystem->put('docker/php-fpm/Dockerfile', $stub);
        // Add nginx dockerfile
        $this->filesystem->createDir('docker/nginx');
        $stub = file_get_contents($this->config->getDockposerDir() . '/stubs/dockerfile-nginx.stub');
        $this->filesystem->put('docker/nginx/Dockerfile', $stub);
        // Add nginx config
        $stub = file_get_contents($this->config->getDockposerDir() . '/stubs/config-nginx.stub');
        $this->filesystem->put('docker/nginx/default.conf', $stub);
        // add docker-compose file to project
        $stub = file_get_contents($this->config->getDockposerDir() . '/stubs/docker-compose.stub');
        $this->filesystem->put('docker-compose.yml', $stub);
    }
}

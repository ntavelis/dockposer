<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer;

use League\Flysystem\Filesystem;
use Ntavelis\Dockposer\Provider\DependenciesProvider;

class DockposerExecutor
{
    /**
     * @var DockposerConfig
     */
    private $config;
    /**
     * @var DependenciesProvider
     */
    private $dependenciesProvider;
    /**
     * @var Filesystem @TODO hide this behind an interface
     */
    private $filesystem;

    public function __construct(DockposerConfig $config, DependenciesProvider $dependenciesProvider, Filesystem $filesystem)
    {
        $this->config = $config;
        $this->dependenciesProvider = $dependenciesProvider;
        $this->filesystem = $filesystem;
    }

    public function run()
    {
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

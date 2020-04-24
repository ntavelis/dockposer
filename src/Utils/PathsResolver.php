<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Utils;

use Ntavelis\Dockposer\DockposerConfig;

class PathsResolver
{
    /**
     * @var DockposerConfig
     */
    private $dockposerConfig;

    public function __construct(DockposerConfig $dockposerConfig)
    {
        $this->dockposerConfig = $dockposerConfig;
    }

    public function getDockerComposeFilePath(): string
    {
        return $this->dockposerConfig->getExecutorConfig('docker_compose_file');
    }

    public function getNginxDockerfilePath(): string
    {
        return $this->getNginxDockerDirPath() . DIRECTORY_SEPARATOR . $this->dockposerConfig->getExecutorConfig('dockerfile_name');
    }

    public function getNginxDockerDirPath(): string
    {
        return $this->getDockerDirPath() . DIRECTORY_SEPARATOR . $this->dockposerConfig->getExecutorConfig('nginx_docker_dir');
    }

    public function getDockerDirPath(): string
    {
        return $this->dockposerConfig->getExecutorConfig('docker_dir');
    }

    public function getPhpFpmDockerfilePath(): string
    {
        return $this->getPhpFpmDockerDirPath() . DIRECTORY_SEPARATOR . $this->dockposerConfig->getExecutorConfig('dockerfile_name');
    }

    public function getPhpFpmDockerDirPath(): string
    {
        return $this->getDockerDirPath() . DIRECTORY_SEPARATOR . $this->dockposerConfig->getExecutorConfig('fpm_docker_dir');
    }

    public function getStubsDirPath(): string
    {
        return $this->dockposerConfig->getDockposerDir() . DIRECTORY_SEPARATOR . 'stubs';
    }
}

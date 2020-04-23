<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer;

class DockposerConfig
{
    /**
     * @var string
     */
    private $dockposerDir;
    /**
     * @var string
     */
    private $baseDir;

    /**
     * @var array
     */
    private $executorsDefaultConfig = [
        'docker_compose_file' => 'docker-compose.yml',
        'docker_dir' => 'docker',
        'nginx_docker_dir' => 'nginx',
        'fpm_docker_dir' => 'php-fpm',
        'dockerfile_name' => 'Dockerfile',
    ];

    /**
     * @var array
     */
    private $executorConfig;

    public function __construct(string $dockposerDir, string $baseDir, array $configOverrides = [])
    {
        $this->dockposerDir = $dockposerDir;
        $this->baseDir = $baseDir;
        $this->executorConfig = array_merge($this->executorsDefaultConfig, $configOverrides);
    }

    public function getDockposerDir(): string
    {
        return $this->dockposerDir;
    }

    public function getBaseDir(): string
    {
        return $this->baseDir;
    }

    public function getExecutorConfig(string $configKey): ?string
    {
        return $this->executorConfig[$configKey] ?? null;
    }


}

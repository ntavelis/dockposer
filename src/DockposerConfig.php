<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer;

use Ntavelis\Dockposer\Utils\PathsResolver;

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
        'nginx_config_file' => 'default.conf',
        'fpm_docker_dir' => 'php-fpm',
        'dockerfile_name' => 'Dockerfile',
    ];
    /**
     * @var array
     */
    private $executorConfig;
    /**
     * @var PathsResolver
     */
    private $pathResolver;

    public function __construct(string $dockposerDir, string $baseDir, array $configOverrides = [])
    {
        $this->dockposerDir = $dockposerDir;
        $this->baseDir = $baseDir;
        $this->executorConfig = array_merge($this->executorsDefaultConfig, $configOverrides);
        $this->pathResolver = new PathsResolver($this);
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

    public function getPathResolver(): PathsResolver
    {
        return $this->pathResolver;
    }
}

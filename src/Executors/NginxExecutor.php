<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Executors;

use Ntavelis\Dockposer\Contracts\ExecutorInterface;
use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\DockposerConfig;
use Ntavelis\Dockposer\Enum\ExecutorStatus;
use Ntavelis\Dockposer\Message\ExecutorResult;

class NginxExecutor implements ExecutorInterface
{
    private const DOCKER_NGINX_DOCKERFILE = 'docker/nginx/Dockerfile';
    /**
     * @var FilesystemInterface
     */
    private $filesystem;
    /**
     * @var DockposerConfig
     */
    private $config;

    public function __construct(FilesystemInterface $filesystem, DockposerConfig $config)
    {
        $this->filesystem = $filesystem;
        $this->config = $config;
    }

    public function execute(): ExecutorResult
    {
        try {
            $this->filesystem->createDir('docker/php-fpm');
            $stub = $this->filesystem->compileStub($this->config->getDockposerDir() . '/stubs/dockerfile-php-fpm.stub');
            $this->filesystem->put(self::DOCKER_NGINX_DOCKERFILE, $stub);
        } catch (\Exception $exception) {
            return new ExecutorResult('Unable to create php-fpm dockerfile, reason: ' . $exception->getMessage(), ExecutorStatus::FAIL);
        }
        return new ExecutorResult('Added php-fpm Dockerfile at ' . self::DOCKER_NGINX_DOCKERFILE, ExecutorStatus::SUCCESS);
    }

    public function supports(string $context): bool
    {
        return $context === 'nginx';
    }
}
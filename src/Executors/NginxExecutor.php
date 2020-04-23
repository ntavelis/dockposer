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
            $this->filesystem->createDir($this->getDockerNginxDir());
            $stub = $this->filesystem->compileStub($this->config->getDockposerDir() . '/stubs/dockerfile-nginx.stub');
            $this->filesystem->put($this->getNginxDockerfilePath(), $stub);
        } catch (\Exception $exception) {
            return new ExecutorResult('Unable to create nginx dockerfile, reason: ' . $exception->getMessage(), ExecutorStatus::FAIL);
        }
        return new ExecutorResult("Added nginx Dockerfile at ./{$this->getNginxDockerfilePath()}", ExecutorStatus::SUCCESS);
    }

    public function shouldExecute(array $context = []): bool
    {
        return !file_exists($this->config->getBaseDir() . DIRECTORY_SEPARATOR . $this->getNginxDockerfilePath());
    }

    private function getNginxDockerfilePath(): string
    {
        return $this->getDockerNginxDir() . DIRECTORY_SEPARATOR . $this->config->getExecutorConfig('dockerfile_name');
    }

    private function getDockerNginxDir(): string
    {
        return $this->config->getExecutorConfig('docker_dir') . DIRECTORY_SEPARATOR . $this->config->getExecutorConfig('nginx_docker_dir');
    }
}

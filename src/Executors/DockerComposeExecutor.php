<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Executors;

use Ntavelis\Dockposer\Contracts\ExecutorInterface;
use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\DockposerConfig;
use Ntavelis\Dockposer\Enum\ExecutorStatus;
use Ntavelis\Dockposer\Message\ExecutorResult;

class DockerComposeExecutor implements ExecutorInterface
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
        $dockerComposeFile = $this->config->getExecutorConfig('docker_compose_file');
        try {
            $stub = $this->filesystem->compileStub($this->config->getDockposerDir() . '/stubs/docker-compose.stub');
            $this->filesystem->put($dockerComposeFile, $stub);
        } catch (\Exception $exception) {
            return new ExecutorResult('Unable to create ' . $dockerComposeFile . ' file, reason: ' . $exception->getMessage(), ExecutorStatus::FAIL);
        }
        return new ExecutorResult('Added docker-compose file, at ./' . $dockerComposeFile, ExecutorStatus::SUCCESS);
    }

    public function shouldExecute(array $context = []): bool
    {
        return !file_exists($this->config->getBaseDir() . DIRECTORY_SEPARATOR . $this->config->getExecutorConfig('docker_compose_file'));
    }
}

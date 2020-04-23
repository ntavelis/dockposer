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
    private const DOCKER_COMPOSE_FILE = 'docker-compose.yml';
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
            $stub = $this->filesystem->compileStub($this->config->getDockposerDir() . '/stubs/docker-compose.stub');
            $this->filesystem->put(self::DOCKER_COMPOSE_FILE, $stub);
        } catch (\Exception $exception) {
            return new ExecutorResult('Unable to create ' . self::DOCKER_COMPOSE_FILE . ' file, reason: ' . $exception->getMessage(), ExecutorStatus::FAIL);
        }
        return new ExecutorResult('Created ' . self::DOCKER_COMPOSE_FILE . ', at ' . self::DOCKER_COMPOSE_FILE, ExecutorStatus::SUCCESS);
    }

    public function shouldExecute(array $context = []): bool
    {
        return !file_exists($this->config->getBaseDir() . DIRECTORY_SEPARATOR . self::DOCKER_COMPOSE_FILE);
    }
}

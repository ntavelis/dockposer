<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Executors;

use Ntavelis\Dockposer\Contracts\ExecutorInterface;
use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\DockposerConfig;
use Ntavelis\Dockposer\Enum\ExecutorStatus;
use Ntavelis\Dockposer\Message\ExecutorResult;

class DockerDirectoryExecutor implements ExecutorInterface
{
    private const DOCKER_DIRECTORY = 'docker';
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
            $this->filesystem->createDir(self::DOCKER_DIRECTORY);
        } catch (\Exception $exception) {
            return new ExecutorResult('Unable to create docker directory, reason: ' . $exception->getMessage(), ExecutorStatus::FAIL);
        }
        return new ExecutorResult('Created docker directory, at ' . self::DOCKER_DIRECTORY, ExecutorStatus::SUCCESS);
    }

    public function supports(string $context): bool
    {
        return $context === 'docker-dir';
    }
}

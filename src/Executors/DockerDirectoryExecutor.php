<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Executors;

use Ntavelis\Dockposer\Contracts\ExecutorInterface;
use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\DockposerConfig;
use Ntavelis\Dockposer\Enum\ExecutorStatus;
use Ntavelis\Dockposer\Exception\UnableToCreateDirectory;
use Ntavelis\Dockposer\Message\ExecutorResult;

class DockerDirectoryExecutor implements ExecutorInterface
{
    private FilesystemInterface $filesystem;
    private DockposerConfig $config;

    public function __construct(FilesystemInterface $filesystem, DockposerConfig $config)
    {
        $this->filesystem = $filesystem;
        $this->config = $config;
    }

    public function execute(): ExecutorResult
    {
        $dockerDirName = $this->config->getPathResolver()->getDockerDirPath();
        try {
            $this->filesystem->createDir($dockerDirName);
        } catch (UnableToCreateDirectory $exception) {
            return new ExecutorResult(
                'Unable to create docker directory, reason: ' . $exception->getMessage(),
                ExecutorStatus::FAIL
            );
        }
        return new ExecutorResult('Created docker directory, at ./' . $dockerDirName, ExecutorStatus::SUCCESS);
    }

    public function shouldExecute(array $context = []): bool
    {
        return !$this->filesystem->dirExists($this->config->getPathResolver()->getDockerDirPath());
    }
}

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
        $dockerDirName = $this->config->getExecutorConfig('docker_dir');
        try {
            $this->filesystem->createDir($dockerDirName);
        } catch (\Exception $exception) {
            return new ExecutorResult('Unable to create docker directory, reason: ' . $exception->getMessage(), ExecutorStatus::FAIL);
        }
        return new ExecutorResult('Created docker directory, at ./' . $dockerDirName, ExecutorStatus::SUCCESS);
    }

    public function shouldExecute(array $context = []): bool
    {
        return !is_dir($this->config->getBaseDir() . DIRECTORY_SEPARATOR . $this->config->getExecutorConfig('docker_dir'));
    }
}

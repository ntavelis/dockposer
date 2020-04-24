<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Executors;

use Ntavelis\Dockposer\Contracts\ExecutorInterface;
use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\DockposerConfig;
use Ntavelis\Dockposer\Enum\ExecutorStatus;
use Ntavelis\Dockposer\Exception\FileNotFoundException;
use Ntavelis\Dockposer\Exception\UnableToCreateDirectory;
use Ntavelis\Dockposer\Exception\UnableToPutContentsToFile;
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
            $this->filesystem->createDir($this->config->getPathResolver()->getNginxDockerDirPath());
            $stub = $this->filesystem->compileStub($this->config->getPathResolver()->getStubsDirPath() . DIRECTORY_SEPARATOR . 'dockerfile-nginx.stub');
            $this->filesystem->put($this->config->getPathResolver()->getNginxDockerfilePath(), $stub);
        } catch (FileNotFoundException | UnableToPutContentsToFile | UnableToCreateDirectory $exception) {
            return new ExecutorResult('Unable to create nginx dockerfile, reason: ' . $exception->getMessage(), ExecutorStatus::FAIL);
        }
        return new ExecutorResult("Added nginx Dockerfile at ./{$this->config->getPathResolver()->getNginxDockerfilePath()}", ExecutorStatus::SUCCESS);
    }

    public function shouldExecute(array $context = []): bool
    {
        return !$this->filesystem->fileExists($this->config->getPathResolver()->getNginxDockerfilePath());
    }
}

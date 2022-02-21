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

class PhpFpmExecutor implements ExecutorInterface
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
        try {
            $this->filesystem->createDir($this->config->getPathResolver()->getPhpFpmDockerDirPath());
            $stubsDirPath = $this->config->getPathResolver()->getStubsDirPath();
            $pathToStub = $stubsDirPath . DIRECTORY_SEPARATOR . 'dockerfile-php-fpm.stub';
            $stub = $this->filesystem->compileStub($pathToStub);
            $this->filesystem->put($this->config->getPathResolver()->getPhpFpmDockerfilePath(), $stub);
        } catch (FileNotFoundException | UnableToPutContentsToFile | UnableToCreateDirectory $exception) {
            return new ExecutorResult(
                'Unable to create php-fpm dockerfile, reason: ' . $exception->getMessage(),
                ExecutorStatus::FAIL
            );
        }
        return new ExecutorResult(
            "Added php-fpm Dockerfile at ./{$this->config->getPathResolver()->getPhpFpmDockerfilePath()}",
            ExecutorStatus::SUCCESS
        );
    }

    public function shouldExecute(array $context = []): bool
    {
        return !$this->filesystem->fileExists($this->config->getPathResolver()->getPhpFpmDockerfilePath());
    }
}

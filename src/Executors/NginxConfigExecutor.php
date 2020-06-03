<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Executors;

use Ntavelis\Dockposer\Contracts\ExecutorInterface;
use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\DockposerConfig;
use Ntavelis\Dockposer\Enum\ExecutorStatus;
use Ntavelis\Dockposer\Exception\FileNotFoundException;
use Ntavelis\Dockposer\Exception\UnableToPutContentsToFile;
use Ntavelis\Dockposer\Message\ExecutorResult;

class NginxConfigExecutor implements ExecutorInterface
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
            $pathToStub = $this->config->getPathResolver()->getStubsDirPath() . DIRECTORY_SEPARATOR . 'nginx-conf.stub';
            $stub = $this->filesystem->compileStub($pathToStub);
            $this->filesystem->put($this->config->getPathResolver()->getNginxConfigFilePath(), $stub);
        } catch (FileNotFoundException | UnableToPutContentsToFile $exception) {
            return new ExecutorResult(
                'Unable to create nginx config file, reason: ' . $exception->getMessage(),
                ExecutorStatus::FAIL
            );
        }
        return new ExecutorResult(
            "Added nginx config file at ./{$this->config->getPathResolver()->getNginxConfigFilePath()}",
            ExecutorStatus::SUCCESS
        );
    }

    public function shouldExecute(array $context = []): bool
    {
        return !$this->filesystem->fileExists($this->config->getPathResolver()->getNginxConfigFilePath());
    }
}

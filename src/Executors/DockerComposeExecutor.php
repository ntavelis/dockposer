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

class DockerComposeExecutor implements ExecutorInterface
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
        $dockerComposeFile = $this->config->getExecutorConfig('docker_compose_file');
        try {
            $stubsDirPath = $this->config->getPathResolver()->getStubsDirPath();
            $pathToStub = $stubsDirPath . DIRECTORY_SEPARATOR . 'docker-compose.stub';
            $stub = $this->filesystem->compileStub($pathToStub);
            $replacedStub = str_replace($this->replaceKeys(), $this->replaceValues(), $stub);
            $this->filesystem->put($dockerComposeFile, $replacedStub);
        } catch (FileNotFoundException | UnableToPutContentsToFile $exception) {
            return new ExecutorResult(
                'Unable to create ' . $dockerComposeFile . ' file, reason: ' . $exception->getMessage(),
                ExecutorStatus::FAIL
            );
        }
        return new ExecutorResult('Added docker-compose file, at ./' . $dockerComposeFile, ExecutorStatus::SUCCESS);
    }

    public function shouldExecute(array $context = []): bool
    {
        return !$this->filesystem->fileExists($this->config->getPathResolver()->getDockerComposeFilePath());
    }

    /**
     * @return string[]
     */
    private function replaceKeys(): array
    {
        return [
            '{{docker_dir}}',
            '{{fpm_docker_dir}}',
            '{{nginx_docker_dir}}',
            '{{dockerfile_name}}',
        ];
    }

    /**
     * @return string[]
     */
    private function replaceValues(): array
    {
        return [
            $this->config->getExecutorConfig('docker_dir'),
            $this->config->getExecutorConfig('fpm_docker_dir'),
            $this->config->getExecutorConfig('nginx_docker_dir'),
            $this->config->getExecutorConfig('dockerfile_name'),
        ];
    }
}

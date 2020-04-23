<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Executors;

use Ntavelis\Dockposer\Contracts\ExecutorInterface;
use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\DockposerConfig;
use Ntavelis\Dockposer\Enum\ExecutorStatus;
use Ntavelis\Dockposer\Message\ExecutorResult;

class PhpFpmExecutor implements ExecutorInterface
{
    private const PHP_FPM_DOCKER_DIR = 'docker/php-fpm';
    private const DOCKER_PHP_FPM_DOCKERFILE = 'Dockerfile';
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
            $this->filesystem->createDir(self::PHP_FPM_DOCKER_DIR);
            $stub = $this->filesystem->compileStub($this->config->getDockposerDir() . '/stubs/dockerfile-php-fpm.stub');
            $this->filesystem->put($this->getPhpFPMDockerfilePath(), $stub);
        } catch (\Exception $exception) {
            return new ExecutorResult('Unable to create php-fpm dockerfile, reason: ' . $exception->getMessage(), ExecutorStatus::FAIL);
        }
        return new ExecutorResult("Added php-fpm Dockerfile at ./{$this->getPhpFPMDockerfilePath()}", ExecutorStatus::SUCCESS);
    }

    public function shouldExecute(array $context = []): bool
    {
        return !file_exists($this->config->getBaseDir() . DIRECTORY_SEPARATOR . $this->getPhpFPMDockerfilePath());
    }

    private function getPhpFPMDockerfilePath(): string
    {
        return self::PHP_FPM_DOCKER_DIR . DIRECTORY_SEPARATOR . self::DOCKER_PHP_FPM_DOCKERFILE;
    }
}

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
use Ntavelis\Dockposer\Provider\PlatformDependenciesProvider;
use Ntavelis\Dockposer\Utils\FileMarker;

class PhpVersionExecutor implements ExecutorInterface
{
    private const TEMPLATE = 'FROM php:{{php_version}}-fpm';
    private const CONFIG_MARKER = 'ntavelis/dockposer/php-docker-image';
    private FilesystemInterface $filesystem;
    private DockposerConfig $config;
    private PlatformDependenciesProvider $platformDependenciesProvider;
    private FileMarker $marker;

    public function __construct(
        FilesystemInterface $filesystem,
        DockposerConfig $config,
        PlatformDependenciesProvider $platformDependenciesProvider
    ) {
        $this->filesystem = $filesystem;
        $this->config = $config;
        $this->platformDependenciesProvider = $platformDependenciesProvider;
        $this->marker = new FileMarker(self::CONFIG_MARKER);
    }

    public function execute(): ExecutorResult
    {
        try {
            $phpFpmDockerfilePath = $this->config->getPathResolver()->getPhpFpmDockerfilePath();
            $initialFileContents = $this->filesystem->readFile($phpFpmDockerfilePath);

            if ($this->marker->isFileMarked($initialFileContents)) {
                $phpVersionString = $this->buildPhpVersionString();
                $content = $this->marker->wrapInMarks($phpVersionString);
                $newFileContents = $this->marker->updateMarkedData($initialFileContents, $content);
                if ($initialFileContents === $newFileContents) {
                    return new ExecutorResult('Nothing to update', ExecutorStatus::SKIPPED);
                }
                $this->filesystem->put($phpFpmDockerfilePath, $newFileContents);

                unset($initialFileContents, $newFileContents); // Memory cleanup
                return new ExecutorResult(
                    "Replaced php version in php-fpm dockerfile ./{$phpFpmDockerfilePath}",
                    ExecutorStatus::SUCCESS
                );
            }
            unset($initialFileContents); // Memory cleanup
            return new ExecutorResult('file not marked', ExecutorStatus::NOT_MARKED);
        } catch (FileNotFoundException | UnableToPutContentsToFile $exception) {
            return new ExecutorResult(
                'Unable to replace php version in php-fpm docker file, reason: ' . $exception->getMessage(),
                ExecutorStatus::FAIL
            );
        }
    }

    public function shouldExecute(array $context = []): bool
    {
        return $this->filesystem->fileExists($this->config->getPathResolver()->getPhpFpmDockerfilePath());
    }

    private function buildPhpVersionString(): string
    {
        return str_replace(
            '{{php_version}}',
            $this->platformDependenciesProvider->getPhpVersion(),
            self::TEMPLATE
        );
    }
}

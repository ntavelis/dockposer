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

class PhpVersionExecutor implements ExecutorInterface
{
    private const TEMPLATE = 'FROM php:{{php_version}}-fpm';
    private const CONFIG_MARKER = 'ntavelis/dockposer/php-docker-image';
    /**
     * @var FilesystemInterface
     */
    private $filesystem;
    /**
     * @var DockposerConfig
     */
    private $config;
    /**
     * @var PlatformDependenciesProvider
     */
    private $platformDependenciesProvider;

    public function __construct(
        FilesystemInterface $filesystem,
        DockposerConfig $config,
        PlatformDependenciesProvider $platformDependenciesProvider
    ) {
        $this->filesystem = $filesystem;
        $this->config = $config;
        $this->platformDependenciesProvider = $platformDependenciesProvider;
    }

    public function execute(): ExecutorResult
    {
        try {
            $initialFileContents = $this->filesystem->readFile($this->config->getPathResolver()->getPhpFpmDockerfilePath());

            if ($this->isFileMarked($initialFileContents)) {
                $buildTemplate = str_replace('{{php_version}}', $this->platformDependenciesProvider->getPhpVersion(), self::TEMPLATE);
                $content = $this->wrapInMarks($buildTemplate);
                $newFileContents = $this->updateData($initialFileContents, $content);
                if ($initialFileContents === $newFileContents) {
                    return new ExecutorResult('Nothing to update', ExecutorStatus::SKIPPED);
                }
                $this->filesystem->put($this->config->getPathResolver()->getPhpFpmDockerfilePath(), $newFileContents);
            }
        } catch (FileNotFoundException| UnableToPutContentsToFile $exception) {
            return new ExecutorResult('Unable to replace php version in php-fpm docker file, reason: ' . $exception->getMessage(), ExecutorStatus::FAIL);
        }

        // Memory cleanup
        unset($initialFileContents, $newFileContents);

        return new ExecutorResult("Replaced php version in php-fpm dockerfile ./{$this->config->getPathResolver()->getPhpFpmDockerfilePath()}", ExecutorStatus::SUCCESS);
    }

    public function shouldExecute(array $context = []): bool
    {
        return $this->filesystem->fileExists($this->config->getPathResolver()->getPhpFpmDockerfilePath());
    }

    private function isFileMarked(string $fileContents): bool
    {
        return false !== strpos($fileContents, sprintf('###> %s ###', self::CONFIG_MARKER));
    }

    private function wrapInMarks(string $buildTemplate): string
    {
        $marker = sprintf('###> %s ###', self::CONFIG_MARKER);
        $content = $marker . "\n";
        $content .= $buildTemplate . "\n";
        $content .= $marker . "\n";

        return $content;
    }

    private function updateData(string $fileContents, string $data): string
    {
        $pieces = explode("\n", trim($data));
        $startMark = trim(reset($pieces));
        $endMark = trim(end($pieces));

        if (false === strpos($fileContents, $startMark) || false === strpos($fileContents, $endMark)) {
            return $fileContents;
        }

        $pattern = '/' . preg_quote($startMark, '/') . '.*?' . preg_quote($endMark, '/') . '/s';
        return preg_replace($pattern, trim($data), $fileContents);
    }
}

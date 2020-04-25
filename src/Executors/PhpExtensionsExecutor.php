<?php

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

class PhpExtensionsExecutor implements ExecutorInterface
{
    private const TEMPLATE = "COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/\nRUN install-php-extensions {{extensions}}";
    private const CONFIG_MARKER = 'ntavelis/dockposer/php-extensions';
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
    /**
     * @var FileMarker
     */
    private $marker;

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
            $initialFileContents = $this->filesystem->readFile($this->config->getPathResolver()->getPhpFpmDockerfilePath());

            $extensionsList = $this->platformDependenciesProvider->getDependencies();
            $extensionsString = implode(' ', $extensionsList);
            $buildTemplate = str_replace('{{extensions}}', $extensionsString, self::TEMPLATE);
            $content = $this->marker->wrapInMarks($buildTemplate);
            $newFileContents = $this->marker->updateMarkedData($initialFileContents, $content);
            if ($initialFileContents === $newFileContents) {
                return new ExecutorResult('Nothing to update', ExecutorStatus::SKIPPED);
            }
            $this->filesystem->put($this->config->getPathResolver()->getPhpFpmDockerfilePath(), $newFileContents);

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
}

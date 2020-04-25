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
use Ntavelis\Dockposer\Utils\PreBundledExtensions;

class PhpExtensionsExecutor implements ExecutorInterface
{
    private const TEMPLATE = "COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/\nRUN install-php-extensions \\\n\t{{extensions}}";
    private const CONFIG_MARKER = 'ntavelis/dockposer/php-extensions';
    private const ZERO_EXTENSIONS_MESSAGE = '# You have installed all the required extensions, or you are requiring prebuild extensions that already exist inside the image';
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

            if ($this->marker->isFileMarked($initialFileContents)) {
                $extensionsString = $this->buildExtensionsString();
                $content = $this->marker->wrapInMarks($extensionsString);
                $newFileContents = $this->marker->updateMarkedData($initialFileContents, $content);
                if ($initialFileContents === $newFileContents) {
                    return new ExecutorResult('Nothing to update', ExecutorStatus::SKIPPED);
                }
                $this->filesystem->put($this->config->getPathResolver()->getPhpFpmDockerfilePath(), $newFileContents);

                unset($initialFileContents, $newFileContents); // Memory cleanup
                return new ExecutorResult("Replaced php extensions in php-fpm dockerfile ./{$this->config->getPathResolver()->getPhpFpmDockerfilePath()}", ExecutorStatus::SUCCESS);
            }
            unset($initialFileContents); // Memory cleanup
            return new ExecutorResult('file not marked', ExecutorStatus::NOT_MARKED);
        } catch (FileNotFoundException | UnableToPutContentsToFile $exception) {
            return new ExecutorResult('Unable to replace php extensions in php-fpm docker file, reason: ' . $exception->getMessage(), ExecutorStatus::FAIL);
        }
    }

    public function shouldExecute(array $context = []): bool
    {
        return $this->filesystem->fileExists($this->config->getPathResolver()->getPhpFpmDockerfilePath());
    }

    /**
     * @return string
     */
    private function buildExtensionsString(): string
    {
        $extensionsListFromComposerJson = $this->platformDependenciesProvider->getDependenciesSorted();
        $preBundledExtensions = PreBundledExtensions::getExtensions();

        $list = array_filter($extensionsListFromComposerJson, function (string $extension) use ($preBundledExtensions) {
            return !in_array($extension, $preBundledExtensions);
        });

        if (count($list) === 0) {
            return self::ZERO_EXTENSIONS_MESSAGE;
        }

        $extensionsListToString = implode(" \\\n\t", $list);
        return str_replace('{{extensions}}', $extensionsListToString, self::TEMPLATE);
    }
}

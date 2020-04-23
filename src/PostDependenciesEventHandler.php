<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer;

use Composer\IO\IOInterface;
use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\Enum\ExecutorStatus;
use Ntavelis\Dockposer\Factory\ExecutorsFactory;
use Ntavelis\Dockposer\Provider\PlatformDependenciesProvider;

class PostDependenciesEventHandler
{
    /**
     * @var DockposerConfig
     */
    private $config;
    /**
     * @var PlatformDependenciesProvider
     */
    private $platformDependenciesProvider;
    /**
     * @var FilesystemInterface
     */
    private $filesystem;
    /**
     * @var IOInterface
     */
    private $io;
    /**
     * @var ExecutorsFactory
     */
    private $executorsFactory;

    public function __construct(
        IOInterface $io,
        ExecutorsFactory $executorsFactory
    ) {
        $this->io = $io;
        $this->executorsFactory = $executorsFactory;
    }

    public function run(): void
    {
        $executors = $this->executorsFactory->createDefaultExecutors();

        foreach ($executors as $executor) {

            if (!$executor->shouldExecute()) {
                continue;
            }

            $result = $executor->execute();

            if ($result->getStatus() === ExecutorStatus::SUCCESS) {
                $this->writeSuccess($result->getResult());
                continue;
            }

            $this->io->writeError($result->getResult());
        }
    }

    /**
     * In order to display a success message we need to use the write error function.
     * Composer kept the interface like that, probably for backwards compatibility
     */
    private function writeSuccess(string $message): void
    {
        $this->io->writeError('<info>' . $message . '<info>');
    }
}

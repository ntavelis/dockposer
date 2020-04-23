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

    public function __construct(
        DockposerConfig $config,
        PlatformDependenciesProvider $platformDependenciesProvider,
        FilesystemInterface $filesystem,
        IOInterface $io
    ) {
        $this->config = $config;
        $this->platformDependenciesProvider = $platformDependenciesProvider;
        $this->filesystem = $filesystem;
        $this->io = $io;
    }

    public function run(): void
    {
        $factory = new ExecutorsFactory($this->config, $this->filesystem);
        $executors = $factory->createDefaultExecutors();

        foreach ($executors as $executor) {

            $result = $executor->execute();

            if ($result->getStatus() === ExecutorStatus::SUCCESS) {
                $this->io->write($result->getResult());
                continue;
            }

            $this->io->writeError($result->getResult());
        }
    }
}

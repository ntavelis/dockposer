<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer;

use Composer\Composer;
use Composer\Config;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\InstallerEvent;
use Composer\Installer\InstallerEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Ntavelis\Dockposer\Factory\ExecutorsFactory;
use Ntavelis\Dockposer\Filesystem\Filesystem;
use Ntavelis\Dockposer\Provider\PlatformDependenciesProvider;

class DockposerPlugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var Composer
     */
    private $composer;
    /**
     * @var IOInterface
     */
    private $io;
    /**
     * @var Config
     */
    private $config;

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
        $this->config = $composer->getConfig();

        $this->io->write('Dockposer Plugin has been activated');
    }

    public function run(InstallerEvent $event = null): void
    {
        $packages = $this->composer->getPackage()->getRequires();
        $dependencies = array_map(function ($version) {
            return (string)$version->getConstraint();
        }, $packages);

        $provider = new PlatformDependenciesProvider($dependencies);

        $baseDir = dirname($this->config->get('vendor-dir'));
        $dockposerDirectory = dirname(__DIR__);
        $extra = $this->composer->getPackage()->getExtra();
        $config = new DockposerConfig($dockposerDirectory, $baseDir, $extra['dockposer'] ?? []);
        $executorsFactory = new ExecutorsFactory($config, new Filesystem($baseDir), $provider);
        $handler = new PostDependenciesEventHandler($this->io, $executorsFactory);
        $handler->run();
    }

    public static function getSubscribedEvents()
    {
        return [
            InstallerEvents::PRE_OPERATIONS_EXEC => 'run',
        ];
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
        // TODO: Implement deactivate() method.
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
        // TODO: Implement uninstall() method.
    }
}

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
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Ntavelis\Dockposer\Provider\DependenciesProvider;

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

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
        $this->config = $composer->getConfig();

        $this->io->write('Plugin has been activated');
    }

    public function postDependenciesSolving(InstallerEvent $event = null)
    {
        $packages = $this->composer->getPackage()->getRequires();
        $dependencies = array_map(function ($version) {
            return (string)$version->getConstraint();
        }, $packages);

        $provider = new DependenciesProvider($dependencies);

        $baseDir = dirname($this->config->get('vendor-dir'));
        $dockposerDirectory = dirname(__DIR__);
        $adapter = new Local($baseDir);
        $filesystem = new Filesystem($adapter);
        $config = new DockposerConfig($dockposerDirectory, $baseDir);
        $executor = new DockposerExecutor($config, $provider, $filesystem);
        $executor->run();
        $this->io->write('postDependenciesSolving :D');
    }

    public static function getSubscribedEvents()
    {
        return [
            InstallerEvents::POST_DEPENDENCIES_SOLVING => 'postDependenciesSolving',
        ];
    }
}

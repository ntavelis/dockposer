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

        // TODO use this in the factory with an executor that will resolve php extensions
        $provider = new PlatformDependenciesProvider($dependencies);

        $baseDir = dirname($this->config->get('vendor-dir'));
        $dockposerDirectory = dirname(__DIR__);
        // TODO pass overrides to the DockposerConfig from composer.json extra section, if there are any
        $config = new DockposerConfig($dockposerDirectory, $baseDir);
        $handler = new PostDependenciesEventHandler($this->io, new ExecutorsFactory($config, new Filesystem($baseDir)));
        $handler->run();
    }

    public static function getSubscribedEvents()
    {
        return [
            InstallerEvents::POST_DEPENDENCIES_SOLVING => 'postDependenciesSolving',
        ];
    }
}

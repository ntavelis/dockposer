<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\InstallerEvent;
use Composer\Installer\InstallerEvents;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
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

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;

        $this->io->write('Plugin has been activated');
    }

    public function postInstallCmd(Event $event = null)
    {
        $this->io->write('postInstallCmd :D');
    }

    public function postPackageInstall(PackageEvent $event = null)
    {
        $this->io->write('postPackageInstall :D');
    }

    public function postDependenciesSolving(InstallerEvent $event = null)
    {
        $packages = $this->composer->getPackage()->getRequires();
        $dependencies = array_map(function ($version){
            return (string)$version->getConstraint();
        }, $packages);

        $provider = new DependenciesProvider($dependencies);
        $dependencies = $provider->getDependencies();
        $this->io->write('postDependenciesSolving :D');
    }

    public static function getSubscribedEvents()
    {
        return [
            InstallerEvents::POST_DEPENDENCIES_SOLVING => 'postDependenciesSolving',
            PackageEvents::POST_PACKAGE_INSTALL => 'postPackageInstall',
            ScriptEvents::POST_INSTALL_CMD => 'postInstallCmd',
        ];
    }
}

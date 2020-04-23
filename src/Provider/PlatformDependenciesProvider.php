<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Provider;

use Ntavelis\Dockposer\Enum\ComposerDependencies;
use Ntavelis\Dockposer\Enum\ComposerVersionIndexes;
use Ntavelis\Dockposer\Utils\Helpers;

class PlatformDependenciesProvider
{
    /**
     * @var array
     */
    private $dependencies;
    /**
     * @var string
     */
    private $phpVersion;

    public function __construct(array $dependencies)
    {
        $this->dependencies = $this->resolveDependencies($dependencies);
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function getPhpVersion()
    {
        return $this->phpVersion;
    }

    private function resolveDependencies(array $dependencies): array
    {
        $resolvedDependencies = [];
        foreach ($dependencies as $dependency => $version) {
            if ($dependency === ComposerDependencies::PHP) {
                $this->resolvePhpVersion($version);
                $resolvedDependencies[] = $dependency;
                continue;
            }

            if (Helpers::stringStartsWith($dependency, ComposerDependencies::EXTERNAL_DEPENDENCIES_PREFIX)) {
                $resolvedDependencies[] = str_replace(ComposerDependencies::EXTERNAL_DEPENDENCIES_PREFIX . '-', '', $dependency);
            }
        }

        return $resolvedDependencies;
    }

    private function resolvePhpVersion(string $version)
    {
        $cleanVersionString = str_replace(['-dev', '[', ']'], '', $version);
        $upperLowerVersionsArray = explode(' ', $cleanVersionString);
        $this->phpVersion = (string)(float)$upperLowerVersionsArray[ComposerVersionIndexes::LOWER_LIMIT_VERSION_POSITION];
    }
}
<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer;

class DockposerConfig
{
    /**
     * @var string
     */
    private $dockposerDir;
    /**
     * @var string
     */
    private $baseDir;
    /**
     * @var string
     */
    private $dockerComposeFileName = 'docker-compose.yml';

    public function __construct(string $dockposerDir, string $baseDir)
    {
        $this->dockposerDir = $dockposerDir;
        $this->baseDir = $baseDir;
    }

    public function getDockposerDir(): string
    {
        return $this->dockposerDir;
    }

    public function getBaseDir(): string
    {
        return $this->baseDir;
    }

    public function getDockerComposeFileName(): string
    {
        return $this->dockerComposeFileName;
    }
}

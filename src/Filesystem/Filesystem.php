<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Filesystem;

use League\Flysystem\FilesystemInterface as LeagueFilesystem;
use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\Exception\FileNotFoundException;

class Filesystem implements FilesystemInterface
{
    /**
     * @var string
     */
    private $pathPrefix;

    public function __construct(string $pathPrefix)
    {
        $this->pathPrefix = rtrim($pathPrefix, '\\/');
    }

    public function put(string $path, string $contents): bool
    {
        $location = $this->applyPathPrefix($path);
        $result = file_put_contents($location, $contents, LOCK_EX);
        if ($result === false) {
            return false;
        }
        return true;
    }

    public function createDir(string $path): bool
    {
        $location = $this->applyPathPrefix($path);
        if (!is_dir($location)) {
            if (false === @mkdir($location, 0777, true)
                || false === is_dir($location)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @throws FileNotFoundException
     */
    public function compileStub(string $stubPath): string
    {
        if (!file_exists($stubPath)) {
            throw new FileNotFoundException();
        }

        return file_get_contents($stubPath) ?? '';
    }

    public function applyPathPrefix(string $path): string
    {
        return $this->pathPrefix . DIRECTORY_SEPARATOR . ltrim($path, '\\/');
    }
}

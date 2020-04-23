<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Filesystem;

use Ntavelis\Dockposer\Contracts\FilesystemInterface;
use Ntavelis\Dockposer\Exception\FileNotFoundException;
use Ntavelis\Dockposer\Exception\UnableToCreateDirectory;
use Ntavelis\Dockposer\Exception\UnableToPutContentsToFile;

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

    /**
     * @throws UnableToPutContentsToFile
     */
    public function put(string $path, string $contents): void
    {
        $location = $this->applyPathPrefix($path);
        $result = @file_put_contents($location, $contents, LOCK_EX);
        if ($result === false) {
            throw new UnableToPutContentsToFile('Unable to update files ' . $location . ' content');
        }
    }

    /**
     * @throws UnableToCreateDirectory
     */
    public function createDir(string $path): void
    {
        $location = $this->applyPathPrefix($path);
        if (!is_dir($location)) {
            if (false === @mkdir($location, 0777, false)
                || false === is_dir($location)) {
                throw new UnableToCreateDirectory('Unable to create directory ' . $location);
            }
        }
    }

    /**
     * @throws FileNotFoundException
     */
    public function compileStub(string $stubPath): string
    {
        $this->fileExists($stubPath);

        return file_get_contents($stubPath) ?? '';
    }

    public function applyPathPrefix(string $path): string
    {
        return $this->pathPrefix . DIRECTORY_SEPARATOR . ltrim($path, '\\/');
    }

    /**
     * @throws FileNotFoundException
     */
    private function fileExists(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new FileNotFoundException('Unable to locate file ' . $filePath);
        }
    }
}

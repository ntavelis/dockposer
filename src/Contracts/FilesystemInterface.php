<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Contracts;

use Ntavelis\Dockposer\Exception\FileNotFoundException;
use Ntavelis\Dockposer\Exception\UnableToCreateDirectory;
use Ntavelis\Dockposer\Exception\UnableToPutContentsToFile;

interface FilesystemInterface
{
    /**
     * @throws UnableToPutContentsToFile
     */
    public function put(string $path, string $contents): void;

    /**
     * @throws UnableToCreateDirectory
     */
    public function createDir(string $dirname): void;

    /**
     * @throws FileNotFoundException
     */
    public function compileStub(string $absolutePathToStub): string;

    public function fileExists(string $filePath): bool;

    public function dirExists(string $dirPath): bool;
}

<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Contracts;

use Ntavelis\Dockposer\Exception\FileNotFoundException;

interface FilesystemInterface
{
    public function createDir(string $dirname): bool;

    public function put(string $path, string $contents): bool;

    /**
     * @throws FileNotFoundException
     */
    public function compileStub(string $stubPath): string;
}

<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Contracts;

interface FilesystemInterface
{
    public function createDir(string $dirname, array $config = []): bool;

    public function put(string $path, string $contents, array $config = []): bool;
}

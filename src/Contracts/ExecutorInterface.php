<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Contracts;

use Ntavelis\Dockposer\Message\ExecutorResult;

interface ExecutorInterface
{
    public function execute(): ExecutorResult;

    public function shouldExecute(array $context = []): bool;
}

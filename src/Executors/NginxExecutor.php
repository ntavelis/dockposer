<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Executors;

use Ntavelis\Dockposer\Contracts\ExecutorInterface;
use Ntavelis\Dockposer\Message\ExecutorResult;

class NginxExecutor implements ExecutorInterface
{

    public function execute(): ExecutorResult
    {
        // TODO: Implement execute() method.
    }

    public function supports(string $context): bool
    {
        return $context === 'nginx';
    }
}

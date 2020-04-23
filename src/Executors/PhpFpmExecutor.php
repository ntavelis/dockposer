<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Provider;

use Ntavelis\Dockposer\Contracts\ExecutorInterface;
use Ntavelis\Dockposer\Message\ExecutorResult;

class PhpFpmExecutor implements ExecutorInterface
{

    public function execute(): ExecutorResult
    {
        // TODO: Implement execute() method.
    }

    public function supports(string $context): bool
    {
        return $context === 'php-fpm';
    }
}

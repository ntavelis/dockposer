<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Enum;

class ExecutorStatus
{
    public const SUCCESS = 'success';
    public const FAIL = 'fail';
    public const SKIPPED = 'skipped';
    public const NOT_MARKED = 'not_marked';
}

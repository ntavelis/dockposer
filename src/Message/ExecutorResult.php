<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Message;

class ExecutorResult
{
    /**
     * @var string
     */
    private $result;
    /**
     * @var string
     */
    private $status;

    public function __construct(string $result, string $status)
    {
        $this->result = $result;
        $this->status = $status;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}

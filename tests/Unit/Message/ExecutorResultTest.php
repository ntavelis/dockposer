<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Tests\Unit\Message;

use Ntavelis\Dockposer\Enum\ExecutorStatus;
use Ntavelis\Dockposer\Message\ExecutorResult;
use PHPUnit\Framework\TestCase;

class ExecutorResultTest extends TestCase
{
    /**
     * @var string
     */
    private $message;

    public function setUp(): void
    {
        parent::setUp();

        $this->message = new ExecutorResult('create file successfully', ExecutorStatus::SUCCESS);
    }

    /** @test */
    public function itCanReturnAResultAndAStatus(): void
    {
        $this->assertSame('create file successfully',$this->message->getResult());
        $this->assertSame(ExecutorStatus::SUCCESS,$this->message->getStatus());
    }
}

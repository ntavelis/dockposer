<?php

declare(strict_types=1);

namespace Unit;

use Composer\IO\IOInterface;
use Ntavelis\Dockposer\Contracts\ExecutorInterface;
use Ntavelis\Dockposer\Enum\ExecutorStatus;
use Ntavelis\Dockposer\Factory\ExecutorsFactory;
use Ntavelis\Dockposer\Message\ExecutorResult;
use Ntavelis\Dockposer\PostDependenciesEventHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PostDependenciesEventHandlerTest extends TestCase
{
    /**
     * @var PostDependenciesEventHandler
     */
    private $executor;
    /**
     * @var ExecutorsFactory|MockObject
     */
    private $executorsFactory;
    /**
     * @var IOInterface|MockObject
     */
    private $io;

    public function setUp(): void
    {
        parent::setUp();

        $this->executorsFactory = $this->createMock(ExecutorsFactory::class);
        $this->io = $this->createMock(IOInterface::class);
        $this->executor = new PostDependenciesEventHandler($this->io, $this->executorsFactory);
    }

    /** @test */
    public function itWillIterateAllTheGivenExecutorsAndGivenThatTheySupportAGivenActionWillCallThemToExecuteTheirLogic(): void
    {
        $executor1 = $this->createMock(ExecutorInterface::class);
        $executor1->expects($this->once())->method('execute');
        $executor1->expects($this->once())->method('shouldExecute')->willReturn(true);
        $executor2 = $this->createMock(ExecutorInterface::class);
        $executor2->expects($this->once())->method('execute');
        $executor2->expects($this->once())->method('shouldExecute')->willReturn(true);
        $this->executorsFactory->expects($this->once())->method('createDefaultExecutors')->willReturn([$executor1, $executor2]);

        $this->executor->run();
    }

    /** @test */
    public function ifTheStatusOfTheExecutorIsSuccessItWritesOutputAsSuccess(): void
    {
        $executor1 = $this->createMock(ExecutorInterface::class);
        $executor1->expects($this->once())->method('execute')->willReturn(new ExecutorResult('Successful operation', ExecutorStatus::SUCCESS));
        $executor1->expects($this->once())->method('shouldExecute')->willReturn(true);
        $this->executorsFactory->expects($this->once())->method('createDefaultExecutors')->willReturn([$executor1]);

        $this->io->expects($this->once())->method('writeError')->with('<info>Successful operation<info>');

        $this->executor->run();
    }

    /** @test */
    public function ifTheStatusOfTheExecutorIsFailItWritesOutputAsError(): void
    {
        $executor1 = $this->createMock(ExecutorInterface::class);
        $executor1->expects($this->once())->method('execute')->willReturn(new ExecutorResult('Operation failed', ExecutorStatus::FAIL));
        $executor1->expects($this->once())->method('shouldExecute')->willReturn(true);
        $this->executorsFactory->expects($this->once())->method('createDefaultExecutors')->willReturn([$executor1]);

        $this->io->expects($this->once())->method('writeError')->with('Operation failed');

        $this->executor->run();
    }

    /** @test */
    public function ifTheStatusOfTheExecutorIsSkippedItDoesNotWriteAnyOutput(): void
    {
        $executor1 = $this->createMock(ExecutorInterface::class);
        $executor1->expects($this->once())->method('execute')->willReturn(new ExecutorResult('', ExecutorStatus::SKIPPED));
        $executor1->expects($this->once())->method('shouldExecute')->willReturn(true);
        $this->executorsFactory->expects($this->once())->method('createDefaultExecutors')->willReturn([$executor1]);

        $this->io->expects($this->never())->method('writeError');
        $this->io->expects($this->never())->method('write');

        $this->executor->run();
    }
}

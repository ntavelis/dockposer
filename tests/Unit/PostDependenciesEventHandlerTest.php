<?php

declare(strict_types=1);

namespace Unit;

use Composer\IO\IOInterface;
use Ntavelis\Dockposer\Contracts\ExecutorInterface;
use Ntavelis\Dockposer\Factory\ExecutorsFactory;
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

    public function setUp(): void
    {
        parent::setUp();

        $this->executorsFactory = $this->createMock(ExecutorsFactory::class);
        $io = $this->createMock(IOInterface::class);
        $this->executor = new PostDependenciesEventHandler($io, $this->executorsFactory);
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
}

<?php

declare(strict_types=1);

namespace Unit;

use Ntavelis\Dockposer\DockposerExecutor;
use PHPUnit\Framework\TestCase;

class DockerExecutorTest extends TestCase
{
    /**
     * @var DockposerExecutor
     */
    private $executor;

    public function setUp(): void
    {
        parent::setUp();

//        $this->executor = new DockposerExecutor();
    }

    /** @test */
    public function itStartsExecutingTheLogicForCreatingTheNecessaryFiles(): void
    {
        //
    }
}

<?php

declare(strict_types=1);

namespace Unit;

use Ntavelis\Dockposer\DockposerConfig;
use PHPUnit\Framework\TestCase;

class DockposerConfigTest extends TestCase
{
    /**
     * @var DockposerConfig
     */
    private $config;

    public function setUp(): void
    {
        parent::setUp();

        $this->config = new DockposerConfig('/srv/app/dockposer', '/srv/app/demoapp');
    }

    /** @test */
    public function itReturnsTheDockposerDirectory(): void
    {
        $this->assertSame($this->config->getDockposerDir(), '/srv/app/dockposer');
    }

    /** @test */
    public function itReturnsTheBaseDirectoryWhereUsuallyTheDirectoryOfTheComposerJsonFile(): void
    {
        $this->assertSame($this->config->getBaseDir(), '/srv/app/demoapp');
    }
}

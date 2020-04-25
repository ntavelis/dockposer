<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Tests\Unit;

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

        $this->config = new DockposerConfig(
            '/srv/app/dockposer',
            '/srv/app/demoapp',
            [
                'docker_dir' => 'overriden',
            ]
        );
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

    /** @test */
    public function ifNotOverriddenItUsesTheDefaultExecutorsConfig(): void
    {
        $this->assertSame('docker-compose.yml', $this->config->getExecutorConfig('docker_compose_file'));
    }

    /** @test */
    public function ifAConfigValueDoesNotExistWeReturnNull(): void
    {
        $this->assertNull($this->config->getExecutorConfig('not_existent'));
    }

    /** @test */
    public function ifTheDefaultConfigIsOverriddenWeReturnTheOverriddenValue(): void
    {
        $this->assertSame('overriden', $this->config->getExecutorConfig('docker_dir'));
    }
}

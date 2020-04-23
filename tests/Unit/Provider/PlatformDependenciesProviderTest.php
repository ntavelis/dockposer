<?php

declare(strict_types=1);

namespace Unit\Provider;

use Ntavelis\Dockposer\Provider\PlatformDependenciesProvider;
use PHPUnit\Framework\TestCase;

class PlatformDependenciesProviderTest extends TestCase
{
    /**
     * @var array
     */
    private $composerDependencies = [
        'php' => '[>= 7.2.5.0-dev < 8.0.0.0-dev]',
        'ext-ctype' => '[]',
        'ext-iconv' => '[]',
        'ntavelis/dockposer' => '== 9999999-dev',
        'symfony/console' => '[>= 5.0.0.0-dev < 5.1.0.0-dev]',
        'symfony/dotenv' => '[>= 5.0.0.0-dev < 5.1.0.0-dev]',
        'symfony/flex' => '[>= 1.3.1.0-dev < 2.0.0.0-dev]',
        'symfony/framework-bundle' => '[>= 5.0.0.0-dev < 5.1.0.0-dev]',
        'symfony/test-pack' => '[>= 1.0.0.0-dev < 2.0.0.0-dev]',
        'symfony/yaml' => '[>= 5.0.0.0-dev < 5.1.0.0-dev]',
    ];

    /**
     * @var PlatformDependenciesProvider
     */
    private $provider;

    public function setUp(): void
    {
        parent::setUp();

        $this->provider = new PlatformDependenciesProvider($this->composerDependencies);
    }

    /** @test */
    public function itCanResolveTheMajorPhpVersion(): void
    {
        $version = $this->provider->getPhpVersion();

        $this->assertSame('7.2', $version);
    }

    /** @test */
    public function itCanResolveTheDependencies(): void
    {
        $dependencies = $this->provider->getDependencies();

        $this->assertSame([
            'php',
            'ctype',
            'iconv',
        ], $dependencies);
    }
}

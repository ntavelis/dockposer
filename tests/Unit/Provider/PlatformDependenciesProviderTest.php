<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Tests\Unit\Provider;

use Ntavelis\Dockposer\Provider\PlatformDependenciesProvider;
use PHPUnit\Framework\TestCase;

class PlatformDependenciesProviderTest extends TestCase
{
    private array $composerDependencies = [
        'php' => '[>= 7.2.5.0-dev < 8.0.0.0-dev]',
        'ext-ctype' => '[]',
        'ext-iconv' => '[]',
        'ext-amqp' => '[]',
        'ntavelis/dockposer' => '== 9999999-dev',
        'symfony/console' => '[>= 5.0.0.0-dev < 5.1.0.0-dev]',
        'symfony/dotenv' => '[>= 5.0.0.0-dev < 5.1.0.0-dev]',
        'symfony/flex' => '[>= 1.3.1.0-dev < 2.0.0.0-dev]',
        'symfony/framework-bundle' => '[>= 5.0.0.0-dev < 5.1.0.0-dev]',
        'symfony/test-pack' => '[>= 1.0.0.0-dev < 2.0.0.0-dev]',
        'symfony/yaml' => '[>= 5.0.0.0-dev < 5.1.0.0-dev]',
    ];

    private PlatformDependenciesProvider $provider;

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
            'ctype',
            'iconv',
            'amqp',
        ], $dependencies);
    }

    /** @test */
    public function ifThePhpVersionIsNotSetInTheComposerJsonFileDefaultToTheVersionOfPhpThatExecutesTheComposer(): void
    {
        $provider = new PlatformDependenciesProvider([
            'ext-ctype' => '[]',
            'ext-iconv' => '[]',
        ]);
        $dependencies = $provider->getDependencies();

        $this->assertSame([
            'ctype',
            'iconv',
        ], $dependencies);

        $this->assertNotEmpty($provider->getPhpVersion());
    }

    /** @test */
    public function itCanResolveTheDependenciesSorted(): void
    {
        $dependencies = $this->provider->getDependenciesSorted();

        $this->assertSame([
            'amqp',
            'ctype',
            'iconv',
        ], $dependencies, 'Dependencies are not Sorted');
    }

    /** @test */
    public function ifWeGetTheDependenciesSortedTheInitialListHasNotBeenAltered(): void
    {
        $this->provider->getDependenciesSorted();

        $dependencies = $this->provider->getDependencies();

        $this->assertSame([
            'ctype',
            'iconv',
            'amqp',
        ], $dependencies);
    }
}

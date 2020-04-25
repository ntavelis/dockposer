<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Tests\Unit\Utils;

use Ntavelis\Dockposer\Utils\PreBundledExtensions;
use PHPUnit\Framework\TestCase;

class PreBundledExtensionsTest extends TestCase
{
    /** @test */
    public function itMaintainsAListWithAllThePreInstalledPhpExtensionsInsideTheOfficialDockerImages(): void
    {
        $extensionsList = PreBundledExtensions::getExtensions();

        $this->assertContainsOnly('string', $extensionsList);
        foreach ($extensionsList as $extension){
            $this->assertSame(strtolower($extension), $extension, 'Pre bundled extension was not in lowercase');
        }
    }
}

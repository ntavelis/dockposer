<?php

declare(strict_types=1);

namespace Unit\Utils;

use Ntavelis\Dockposer\Utils\Helpers;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    /** @test */
    public function itCanDecideIfAGivenStringStartsWithAnotherString(): void
    {
        $string = 'This is a string';

        $this->assertTrue(Helpers::stringStartsWith($string, 'This'));
        $this->assertFalse(Helpers::stringStartsWith($string, 'No'));
    }
}

<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Utils;

class Helpers
{
    /**
     * Determine if a given string starts with a given substring.
     */
    public static function stringStartsWith(string $haystack, string $needle): bool
    {
        if ($needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0) {
            return true;
        }

        return false;
    }
}

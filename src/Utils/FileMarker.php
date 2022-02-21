<?php

declare(strict_types=1);

namespace Ntavelis\Dockposer\Utils;

class FileMarker
{
    private string $markerName;

    public function __construct(string $marker_name)
    {
        $this->markerName = $marker_name;
    }

    public function isFileMarked(string $fileContents): bool
    {
        return false !== strpos($fileContents, sprintf('###> %s ###', $this->markerName));
    }

    public function wrapInMarks(string $buildTemplate): string
    {
        $marker = sprintf('###> %s ###', $this->markerName);
        $content = $marker . "\n";
        $content .= $buildTemplate . "\n";
        $content .= $marker;

        return $content;
    }

    public function updateMarkedData(string $fileContents, string $data): string
    {
        $pieces = explode("\n", trim($data));
        $startMark = trim(reset($pieces));
        $endMark = trim(end($pieces));

        if (false === strpos($fileContents, $startMark) || false === strpos($fileContents, $endMark)) {
            return $fileContents;
        }

        $pattern = '/' . preg_quote($startMark, '/') . '.*?' . preg_quote($endMark, '/') . '/s';
        return preg_replace($pattern, trim($data), $fileContents);
    }
}

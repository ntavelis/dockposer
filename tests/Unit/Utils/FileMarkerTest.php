<?php

declare(strict_types=1);

namespace Unit\Utils;

use Ntavelis\Dockposer\Utils\FileMarker;
use PHPUnit\Framework\TestCase;

class FileMarkerTest extends TestCase
{
    /**
     * @var FileMarker
     */
    private $fileMarker;

    public function setUp(): void
    {
        parent::setUp();

        $this->fileMarker = new FileMarker('ntavelis/dockposer/demo');
    }

    /** @test */
    public function itCanDecideIfAFileContainsAMarkedSection(): void
    {
        $contents = '';

        $this->assertFalse($this->fileMarker->isFileMarked($contents));

        $contents = "###> ntavelis/dockposer/demo ###\nMarkedSection\n###> ntavelis/dockposer/demo ###";
        $this->assertTrue($this->fileMarker->isFileMarked($contents));
    }

    /** @test */
    public function ifTheFileIsMarkedOnlyWithOpenSectionWeConsiderItNotMarked(): void
    {
        $contents = "###> another_marker_name ###\nBrokenMarkedSection\n";
        $this->assertFalse($this->fileMarker->isFileMarked($contents));
    }

    /** @test */
    public function ifTheFileIsMarkedOnlyWithCloseSectionWeConsiderItNotMarked(): void
    {
        $contents = "BrokenMarkedSection\n###> another_marker_name ###\nBrokenMarkedSection";
        $this->assertFalse($this->fileMarker->isFileMarked($contents));
    }

    /** @test */
    public function ifThereIsAMarkWithWrongMarkNameWeIgnoreIt(): void
    {
        $contents = "###> another_marker_name ###\nMarkedSection\n###> another_marker_name ###";
        $this->assertFalse($this->fileMarker->isFileMarked($contents));
    }

    /** @test */
    public function itCanWrapInMarksAnyString(): void
    {
        $expects = "###> ntavelis/dockposer/demo ###\nMarkedSection\n###> ntavelis/dockposer/demo ###";

        $stringToBeMarked = 'MarkedSection';

        $this->assertSame($expects, $this->fileMarker->wrapInMarks($stringToBeMarked));
    }

    /** @test */
    public function itCanUpdateAFileContentsOnlyInMarkedSection(): void
    {
        $fileContents = "###> ntavelis/dockposer/demo ###\nMarkedSection\n###> ntavelis/dockposer/demo ###";

        $updatedMarkedSectionText = 'Updated marked section';

        $expects = "###> ntavelis/dockposer/demo ###\nUpdated marked section\n###> ntavelis/dockposer/demo ###";
        $actual = $this->fileMarker->updateMarkedData($fileContents, $this->fileMarker->wrapInMarks($updatedMarkedSectionText));
        $this->assertSame($expects, $actual);
    }

    /** @test */
    public function sectionOutsideOfMarkedAreaWillNotBeTouched(): void
    {
        $fileContents = "###> ntavelis/dockposer/demo ###\nMarkedSection\n###> ntavelis/dockposer/demo ###\n\nOther Config\nOther Config";

        $updatedMarkedSectionText = 'Updated marked section';

        $expects = "###> ntavelis/dockposer/demo ###\nUpdated marked section\n###> ntavelis/dockposer/demo ###\n\nOther Config\nOther Config";
        $actual = $this->fileMarker->updateMarkedData($fileContents, $this->fileMarker->wrapInMarks($updatedMarkedSectionText));
        $this->assertSame($expects, $actual);
    }

    /** @test */
    public function ifTheFileContainsMultipleMarkedSectionsItWillOnlyUpdateTheSectionWithOutMarkerName(): void
    {
        $fileContents = "###> ntavelis/dockposer/demo ###\nMarkedSection\n###> ntavelis/dockposer/demo ###\n\nOther Config\nOther Config" .
            "###> another_marker_name ###\nMarkedSection\n###> another_marker_name ###";

        $updatedMarkedSectionText = 'Updated marked section';

        $expects = "###> ntavelis/dockposer/demo ###\nUpdated marked section\n###> ntavelis/dockposer/demo ###\n\nOther Config\nOther Config" .
            "###> another_marker_name ###\nMarkedSection\n###> another_marker_name ###";
        $actual = $this->fileMarker->updateMarkedData($fileContents, $this->fileMarker->wrapInMarks($updatedMarkedSectionText));
        $this->assertSame($expects, $actual);
    }
}

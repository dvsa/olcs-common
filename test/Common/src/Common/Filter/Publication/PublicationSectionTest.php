<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\PublicationSection;
use Common\Data\Object\Publication;

/**
 * Class PublicationSectionTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PublicationSectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider filterProvider
     *
     * @param $section
     * @param $expectedOutput
     */
    public function testFilter($section, $expectedOutput)
    {
        $input = new Publication(['publicationSectionConst' => $section]);
        $sut = new PublicationSection();

        $output = $sut->filter($input);

        $this->assertEquals($output->offsetGet('publicationSection'), $expectedOutput);
    }

    /**
     * Filter data provider
     */
    public function filterProvider()
    {
        return [
            ['hearingSectionId', 13],
            ['decisionSectionId', 14]
        ];
    }
}

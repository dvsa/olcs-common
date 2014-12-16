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
     * @group publicationFilter
     *
     * @param $section
     * @param $expectedOutput
     */
    public function testFilter($section, $expectedOutput)
    {
        $input = new Publication(['publicationSectionConst' => $section]);
        $sut = new PublicationSection();

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->offsetGet('publicationSection'));
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

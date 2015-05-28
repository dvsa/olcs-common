<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\ApplicationBusNote;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class ApplicationBusNoteTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ApplicationBusNoteTest extends MockeryTestCase
{
    /**
     * @dataProvider filterProvider
     *
     * @param $section
     * @param $expectedString
     *
     * @group publicationFilter
     *
     * Test the application bus note filter
     */
    public function testFilter($section, $expectedString)
    {
        $sut = new ApplicationBusNote();

        $inputData = [
            'licType' => $sut::PSV_LIC_TYPE,
            'publicationSection' => $section
        ];

        $expectedOutput = [
            'licType' => $sut::PSV_LIC_TYPE,
            'publicationSection' => $section,
            'busNote' => sprintf($sut::BUS_STRING, $expectedString)
        ];

        $input = new Publication($inputData);

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->getArrayCopy());
    }

    /**
     * Filter provider
     *
     * @return array
     */
    public function filterProvider()
    {
        $sut = new ApplicationBusNote();

        return [
            [$sut::LIC_SURRENDERED_SECTION, $sut::BUS_SURRENDERED],
            [$sut::LIC_REVOKED_SECTION, $sut::BUS_REVOKED],
            [$sut::LIC_CNS_SECTION, $sut::BUS_CNS]
        ];
    }
}

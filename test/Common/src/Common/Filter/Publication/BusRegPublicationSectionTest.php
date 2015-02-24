<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\BusRegPublicationSection;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class BusRegPublicationSectionTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegPublicationSectionTest extends MockeryTestCase
{
    /**
     * @dataProvider filterProvider
     *
     * @param $previousStatus
     * @param $isShortNotice
     * @param $expectedSection
     *
     * @group publicationFilter
     *
     * Test the bus reg publication section filter
     */
    public function testFilter($previousStatus, $isShortNotice, $expectedSection)
    {
        $busRegData = [
            'isShortNotice' => $isShortNotice
        ];

        $inputData = [
            'busRegData' => $busRegData,
            'previousStatus' => $previousStatus
        ];

        $expectedOutput = [
            'busRegData' => $busRegData,
            'previousStatus' => $previousStatus,
            'publicationSection' => $expectedSection
        ];

        $input = new Publication($inputData);
        $sut = new BusRegPublicationSection();

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
        return [
            ['breg_s_new', 'N', 21],
            ['breg_s_new', 'Y', 22],
            ['breg_s_var', 'N', 23],
            ['breg_s_var', 'Y', 24],
            ['breg_s_cancellation', 'N', 25],
            ['breg_s_cancellation', 'Y', 26],
        ];
    }
}

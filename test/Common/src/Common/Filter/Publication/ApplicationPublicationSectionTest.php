<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\ApplicationPublicationSection;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class ApplicationPublicationSectionTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ApplicationPublicationSectionTest extends MockeryTestCase
{
    /**
     * Tests the filter throws exception when section not found
     *
     * @expectedException \Common\Exception\ResourceNotFoundException
     * @group publicationFilter
     */
    public function testMissingSectionException()
    {
        $appData = [
            'status' => [
                'id' => 'made_up_status'
            ]
        ];

        $inputData = [
            'applicationData' => $appData
        ];

        $input = new Publication($inputData);
        $sut = new ApplicationPublicationSection();

        $sut->filter($input);
    }

    /**
     * @dataProvider filterProvider
     *
     * @param $appStatus
     * @param $expectedSection
     *
     * @group publicationFilter
     *
     * Test the application publication section filter
     */
    public function testFilter($appStatus, $expectedSection)
    {
        $appData = [
            'status' => [
                'id' => $appStatus
            ]
        ];

        $inputData = [
            'applicationData' => $appData
        ];

        $expectedOutput = [
            'applicationData' => $appData,
            'publicationSection' => $expectedSection
        ];

        $input = new Publication($inputData);
        $sut = new ApplicationPublicationSection();

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
        $sut = new ApplicationPublicationSection();

        return [
            [$sut::APP_NEW_STATUS, $sut::APP_NEW_SECTION],
            [$sut::APP_GRANTED_STATUS, $sut::APP_GRANTED_SECTION],
            [$sut::APP_REFUSED_STATUS, $sut::APP_REFUSED_SECTION],
            [$sut::APP_WITHDRAWN_STATUS, $sut::APP_WITHDRAWN_SECTION]
        ];
    }
}

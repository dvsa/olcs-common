<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\ApplicationPubType;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class ApplicationPubTypeTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ApplicationPubTypeTest extends MockeryTestCase
{
    /**
     * @dataProvider filterProvider
     *
     * @param $licType
     * @param $pubType
     *
     * @group publicationFilter
     *
     * Test the application pub type filter
     */
    public function testFilter($licType, $pubType)
    {
        $appData = [
            'goodsOrPsv' => [
                'id' => $licType
            ]
        ];

        $inputData = [
            'applicationData' => $appData
        ];

        $expectedOutput = [
            'applicationData' => $appData,
            'publicationType' => $pubType
        ];

        $input = new Publication($inputData);
        $sut = new ApplicationPubType();

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
        $sut = new ApplicationPubType();

        return [
            [$sut::GV_LIC_TYPE, 'A&D'],
            ['other_status', 'N&P'],
        ];
    }
}

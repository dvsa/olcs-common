<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\ApplicationText2;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class ApplicationText2Test
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ApplicationText2Test extends MockeryTestCase
{
    /**
     * @dataProvider filterProvider
     *
     * @param $licType
     * @param $publicationSection
     * @param $licenceCancelled
     * @param $calledFunction
     *
     * @group publicationFilter
     *
     * Test the application bus note filter
     */
    public function testFilter($licType, $publicationSection, $licenceCancelled, $calledFunction)
    {
        $sut = new ApplicationText2();

        $licNo = 'OB1234567';
        $licenceType = 'SN';
        $organisationName = 'Organisation Name';
        $organisationTradingName = 'Organisation Trading Name';
        $organisationType = 'org_t_rc';
        $personData = [
            'forename' => 'John',
            'familyName' => 'Smith',
        ];

        $licenceData = [
            'licNo' => $licNo,
            'licenceType' => [
                'olbsKey' => $licenceType
            ],
            'organisation' => [
                'name' => $organisationName,
                'type' => $organisationType,
                'tradingNames' => [
                    0 => [
                        'name' => $organisationTradingName
                    ]
                ],
                'organisationPersons' => [
                    0 => [
                        'person' => $personData
                    ]
                ]
            ]
        ];

        $inputData = [
            'licType' => $licType,
            'licenceData' => $licenceData,
            'publicationSection' => $publicationSection
        ];

        //there's an offset exists on licence cancelled in the code
        $inputData = array_merge($inputData, $licenceCancelled);

        $input = new Publication($inputData);

        $output = $sut->filter($input)->offsetGet('text3');

        //$this->assertEquals(implode("\n", $sut->$calledFunction($input, $licenceData, [])), $output);
    }

    /**
     * Filter provider
     *
     * @return array
     */
    public function filterProvider()
    {
        $sut = new ApplicationText2();

        return [
            [$sut::GV_LIC_TYPE, $sut::APP_GRANTED_SECTION, [], 'getAllData'],
            [$sut::GV_LIC_TYPE, $sut::APP_WITHDRAWN_SECTION, [], 'getLicenceInfo'],
            [$sut::GV_LIC_TYPE, $sut::APP_REFUSED_SECTION, [], 'getAllData'],
            [$sut::GV_LIC_TYPE, 'some_status', [], 'getAllData'],
            [$sut::GV_LIC_TYPE, 'some_status',['licenceCancelled' => 'licence cancelled'], 'getGvCancelled'],
            [$sut::PSV_LIC_TYPE, 'some_status',['licenceCancelled' => 'licence cancelled'], 'getPsvCancelled'],
            [$sut::PSV_LIC_TYPE, 'some_status', [], 'getAllData']
        ];
    }
}

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
     * @dataProvider filterCallsCancelledProvider
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
    public function testFilterCallsCancelled($licType, $publicationSection, $licenceCancelled, $calledFunction)
    {
        $sut = new ApplicationText2();

        $input = $this->getFilterInput($licType, $publicationSection, $licenceCancelled);
        $output = $this->getFilterResult($input);

        $this->assertEquals(implode("\n", $sut->$calledFunction($input, [])), $output->offsetGet('text2'));
    }

    /**
     * @dataProvider filterCallsGetLicenceInfoProvider
     *
     * @group publicationFilter
     *
     * @param $licType
     * @param $publicationSection
     */
    public function testFilterCallsGetLicenceInfo($licType, $publicationSection)
    {
        $sut = new ApplicationText2();

        $input = $this->getFilterInput($licType, $publicationSection, []);
        $output = $this->getFilterResult($input);
        $this->assertEquals($sut->getLicenceInfo($output->offsetGet('licenceData')), $output->offsetGet('text2'));
    }

    /**
     * @dataProvider filterCallsGetAllDataProvider
     *
     * @group publicationFilter
     *
     * @param $licType
     * @param $publicationSection
     */
    public function testFilterCallsGetAllData($licType, $publicationSection)
    {
        $sut = new ApplicationText2();

        $input = $this->getFilterInput($licType, $publicationSection, []);
        $output = $this->getFilterResult($input);
        $this->assertEquals(
            implode(
                "\n",
                $sut->getAllData($output->offsetGet('licenceData'), [])
            ),
            $output->offsetGet('text2')
        );
    }

    /**
     * Gets the publication object
     *
     * @param $licType
     * @param $publicationSection
     * @param $licenceCancelled
     * @return Publication
     */
    public function getFilterInput($licType, $publicationSection, $licenceCancelled)
    {
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

        return new Publication($inputData);
    }

    /**
     * Gets the result from a filter
     *
     * @param \Common\Data\Object\Publication $input
     * @return \Common\Data\Object\Publication
     */
    public function getFilterResult($input)
    {
        $sut = new ApplicationText2();
        return $sut->filter($input);
    }

    /**
     * Filter provider
     *
     * @return array
     */
    public function filterCallsCancelledProvider()
    {
        $sut = new ApplicationText2();

        return [
            [$sut::GV_LIC_TYPE, 'some_status', ['licenceCancelled' => 'licence cancelled'], 'getGvCancelled'],
            [$sut::PSV_LIC_TYPE, 'some_status', ['licenceCancelled' => 'licence cancelled'], 'getPsvCancelled']
        ];
    }

    /**
     * Provider for when filter is expected to call getAllData
     *
     * @return array
     */
    public function filterCallsGetAllDataProvider()
    {
        $sut = new ApplicationText2();

        return [
            [$sut::GV_LIC_TYPE, $sut::APP_GRANTED_SECTION],
            [$sut::GV_LIC_TYPE, $sut::APP_REFUSED_SECTION],
            [$sut::GV_LIC_TYPE, 'some_status'],
            [$sut::PSV_LIC_TYPE, 'some_status']
        ];
    }

    /**
     * Provider for when filter is expected to call getLicenceInfo
     *
     * @return array
     */
    public function filterCallsGetLicenceInfoProvider()
    {
        $sut = new ApplicationText2();

        return [
            [$sut::GV_LIC_TYPE, $sut::APP_WITHDRAWN_SECTION]
        ];
    }
}

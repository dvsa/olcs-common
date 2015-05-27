<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\ApplicationText3;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class ApplicationText3Test
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ApplicationText3Test extends MockeryTestCase
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
        $sut = new ApplicationText3();

        $operatingCentre1 = 'operating centre 1';
        $operatingCentre2 = 'operating centre 2';
        $conditionUndertaking1 = 'condition undertaking 1';
        $conditionUndertaking2 = 'condition undertaking 2';
        $transportManagers = 'Transport Manager(s): text';
        $busNote = 'bus note';
        $licenceAddress = 'Licence address';

        $operatingCentres = [
            0 => $operatingCentre1,
            1 => $operatingCentre2,
        ];

        $conditionUndertaking = [
            0 => $conditionUndertaking1,
            1 => $conditionUndertaking2,
        ];

        $inputData = [
            'licType' => $licType,
            'licenceAddress' => $licenceAddress,
            'publicationSection' => $publicationSection,
            'busNote' => $busNote,
            'operatingCentres' => $operatingCentres,
            'transportManagers' => $transportManagers,
            'conditionUndertaking' => $conditionUndertaking
        ];

        //there's an offset exists on licence cancelled in the code
        $inputData = array_merge($inputData, $licenceCancelled);

        $input = new Publication($inputData);

        $output = $sut->filter($input)->offsetGet('text3');

        $this->assertEquals(implode("\n", $sut->$calledFunction($input, [])), $output);
    }

    /**
     * Filter provider
     *
     * @return array
     */
    public function filterProvider()
    {
        $sut = new ApplicationText3();

        return [
            [$sut::GV_LIC_TYPE, $sut::APP_GRANTED_SECTION, [], 'getPartialData'],
            [$sut::GV_LIC_TYPE, $sut::APP_WITHDRAWN_SECTION, [], 'getPartialData'],
            [$sut::GV_LIC_TYPE, $sut::APP_REFUSED_SECTION, [], 'getPartialData'],
            [$sut::GV_LIC_TYPE, 'some_status', [], 'getAllData'],
            [$sut::GV_LIC_TYPE, 'some_status',['licenceCancelled' => ''], 'getPartialData'],
            [$sut::PSV_LIC_TYPE, 'some_status', [], 'getAllData']
        ];
    }
}

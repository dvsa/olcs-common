<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\ApplicationLicenceCancelled;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class ApplicationLicenceCancelledTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ApplicationLicenceCancelledTest extends MockeryTestCase
{
    /**
     * @dataProvider filterProvider
     *
     * @param $publicationSection
     * @param $expectedString
     *
     * @group publicationFilter
     *
     * Test the application licence cancelled filter
     */
    public function testFilter($publicationSection, $expectedString)
    {
        $inputData = [
            'publicationSection' => $publicationSection
        ];

        $input = new Publication($inputData);
        $sut = new ApplicationLicenceCancelled();

        $output = $sut->filter($input)->getArrayCopy();

        $this->assertEquals($expectedString . $sut->getDate(), $output['licenceCancelled']);
    }

    /**
     * Filter provider
     *
     * @return array
     */
    public function filterProvider()
    {
        $sut = new ApplicationLicenceCancelled();

        return [
            [$sut::LIC_SURRENDERED_SECTION, $sut::LIC_SURRENDERED],
            [$sut::LIC_TERMINATED_SECTION, $sut::LIC_TERMINATED],
            [$sut::LIC_CNS_SECTION, $sut::LIC_CNS]
        ];
    }
}

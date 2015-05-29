<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\ApplicationText1;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class ApplicationText1Test
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ApplicationText1Test extends MockeryTestCase
{
    /**
     * @group publicationFilter
     *
     * Test the application text1 filter
     */
    public function testFilter()
    {
        $sut = new ApplicationText1();

        $licNo = 12345;
        $licenceType = 'licence type';
        $previousPublication = 67890;

        $licenceData = [
            'licNo' => $licNo,
            'licenceType' => [
                'olbsKey' => $licenceType
            ]
        ];

        $inputData = [
            'licenceData' => $licenceData,
            'previousPublication' => $previousPublication
        ];

        $expectedOutput = [
            'licenceData' => $licenceData,
            'previousPublication' => $previousPublication,
            'text1' => $licNo . $licenceType . ' (Previous Publication:(' . $previousPublication . '))'
        ];

        $input = new Publication($inputData);

        $this->assertEquals($expectedOutput, $sut->filter($input)->getArrayCopy());
    }
}

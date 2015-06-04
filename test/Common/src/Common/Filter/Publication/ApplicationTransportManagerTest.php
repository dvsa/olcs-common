<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\ApplicationTransportManager;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class ApplicationTransportManagerTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ApplicationTransportManagerTest extends MockeryTestCase
{
    /**
     * @group publicationFilter
     *
     * Test the application transport manager filter
     */
    public function testFilter()
    {
        $forename = 'forename';
        $familyName = 'family name';
        $forename2 = 'forename2';
        $familyName2 = 'family name2';

        $inputData = [
            'transportManagerData' => [
                0 => [
                    'transportManager' => [
                        'homeCd' => [
                            'person' => [
                                'forename' => $forename,
                                'familyName' => $familyName
                            ]
                        ]
                    ]
                ],
                1 => [
                    'transportManager' => [
                        'homeCd' => [
                            'person' => [
                                'forename' => $forename2,
                                'familyName' => $familyName2
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $newData = [
            'transportManagers' => $forename . ' ' . $familyName . ', ' . $forename2 . ' ' . $familyName2
        ];

        $expectedData = array_merge($newData, $inputData);

        $input = new Publication($inputData);
        $sut = new ApplicationTransportManager();

        $output = $sut->filter($input);

        $this->assertEquals($expectedData, $output->getArrayCopy());
    }
}

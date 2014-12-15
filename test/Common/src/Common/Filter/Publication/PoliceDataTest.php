<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\PoliceData;
use Common\Data\Object\Publication;

/**
 * Class PoliceDataTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PoliceDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group publicationFilter
     */
    public function testFilter()
    {
        $licenceData = [
            'organisation' => [
                'organisationPersons' => [
                    0 => [
                        'person' => [
                            'forename' => 'John',
                            'familyName' => 'Smith',
                            'birthDate' => '1979-03-04'
                        ]
                    ],
                    1 => [
                        'person' => [
                            'forename' => 'Alan',
                            'familyName' => 'Jones',
                            'birthDate' => '1972-09-21'
                        ]
                    ]
                ]
            ],
        ];

        $expectedOutput = [
            0 => [
                'forename' => 'John',
                'familyName' => 'Smith',
                'birthDate' => '1979-03-04'
            ],
            1 => [
                'forename' => 'Alan',
                'familyName' => 'Jones',
                'birthDate' => '1972-09-21'
            ]
        ];

        $input = new Publication(['licenceData' => $licenceData]);
        $sut = new PoliceData();

        $output = $sut->filter($input);

        $this->assertEquals($output->offsetGet('policeData'), $expectedOutput);
    }
}

<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\LicenceAddress;
use Common\Data\Object\Publication;

/**
 * Class LicenceAddressTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class LicenceAddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider filterProvider
     *
     * @param array $addressData
     * @param string $expectedOutput
     */
    public function testFilter($addressData, $expectedOutput)
    {
        $licenceData = [
            'contactDetails' => [
                0 => [
                    'address' => $addressData
                ]
            ],
        ];

        $input = new Publication(['licenceData' => $licenceData]);
        $sut = new LicenceAddress();

        $output = $sut->filter($input);

        $this->assertEquals($output->offsetGet('licenceAddress'), $expectedOutput);
    }

    public function filterProvider()
    {
        return [
            [
                [
                    'addressLine1' => 'line 1',
                    'addressLine2' => 'line 2',
                    'addressLine3' => '',
                    'addressLine4' => '',
                    'town' => 'town',
                    'postcode' => 'postcode',
                ],
                'line 1, line 2, town, postcode'
            ],
            [
                [
                    'addressLine1' => 'line 1',
                    'addressLine2' => 'line 2',
                    'addressLine3' => 'line 3',
                    'addressLine4' => 'line 4',
                    'town' => 'town',
                    'postcode' => 'postcode',
                ],
                'line 1, line 2, line 3, line 4, town, postcode'
            ],
        ];
    }
}

<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\OperatingCentreAddress;
use Common\Data\Object\Publication;

/**
 * Class OperatingCentreAddressTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class OperatingCentreAddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group publicationFilter
     *
     * @dataProvider filterProvider
     *
     * @param array $addressData
     * @param string $expectedOutput
     */
    public function testFilter($addressData, $expectedOutput)
    {
        $input = new Publication(['operatingCentreAddressData' => $addressData]);
        $sut = new OperatingCentreAddress();

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->offsetGet('operatingCentreAddress'));
    }

    /**
     * Data provider for testFilter
     *
     * @return array
     */
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

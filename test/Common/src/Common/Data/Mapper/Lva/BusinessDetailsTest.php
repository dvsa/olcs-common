<?php

/**
 * Business Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Data\Mapper\Lva;

use PHPUnit_Framework_TestCase;
use Common\Data\Mapper\Lva\BusinessDetails;

/**
 * Business Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessDetailsTest extends PHPUnit_Framework_TestCase
{
    public function testMapFromResult()
    {
        $input = [
            'name' => 'Foo ltd',
            'type' => [
                'id' => 'TYPE'
            ],
            'companyOrLlpNo' => '12345678',
            'version' => 11,
            'tradingNames' => [
                ['name' => 'Foo'],
                ['name' => 'Bar']
            ],
            'natureOfBusiness' => 'SIC Code 1',
            'contactDetails' => [
                'address' => [
                    'foo' => 'bar'
                ]
            ]
        ];

        $output = BusinessDetails::mapFromResult($input);

        $expected = [
            'version' => 11,
            'data' => [
                'name' => 'Foo ltd',
                'type' => 'TYPE',
                'companyNumber' => [
                    'company_number' => '12345678'
                ],
                'tradingNames' => [
                    'trading_name' => [
                        'Foo',
                        'Bar'
                    ]
                ],
                'natureOfBusiness' => 'SIC Code 1',
            ],
            'registeredAddress' => [
                'foo' => 'bar'
            ]
        ];

        $this->assertEquals($expected, $output);
    }
}

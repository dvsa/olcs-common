<?php

/**
 * Business Details Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessRule\Rule;

use PHPUnit_Framework_TestCase;
use Common\BusinessRule\Rule\BusinessDetails;

/**
 * Business Details Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessDetailsTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new BusinessDetails();
    }

    /**
     * @dataProvider providerValidate
     */
    public function testValidate($orgId, $data, $natureOfBusinesses, $contactDetailsId, $expected)
    {
        $this->assertEquals($expected, $this->sut->validate($orgId, $data, $natureOfBusinesses, $contactDetailsId));
    }

    public function providerValidate()
    {
        return [
            [
                111,
                [
                    'version' => 1
                ],
                [
                    ['foo' => 'bar'],
                    ['foo' => 'cake']
                ],
                null,
                [
                    'id' => 111,
                    'version' => 1,
                    'natureOfBusinesses' => [
                        ['foo' => 'bar'],
                        ['foo' => 'cake']
                    ]
                ]
            ],
            [
                111,
                [
                    'version' => 1,
                    'data' => [
                        'companyNumber' => [
                            'company_number' => '123456789'
                        ]
                    ]
                ],
                [
                    ['foo' => 'bar'],
                    ['foo' => 'cake']
                ],
                null,
                [
                    'id' => 111,
                    'version' => 1,
                    'natureOfBusinesses' => [
                        ['foo' => 'bar'],
                        ['foo' => 'cake']
                    ],
                    'companyOrLlpNo' => '123456789'
                ]
            ],
            [
                111,
                [
                    'version' => 1,
                    'data' => [
                        'name' => 'Foo ltd',
                        'companyNumber' => [
                            'company_number' => '123456789'
                        ]
                    ]
                ],
                [
                    ['foo' => 'bar'],
                    ['foo' => 'cake']
                ],
                null,
                [
                    'id' => 111,
                    'version' => 1,
                    'natureOfBusinesses' => [
                        ['foo' => 'bar'],
                        ['foo' => 'cake']
                    ],
                    'companyOrLlpNo' => '123456789',
                    'name' => 'Foo ltd'
                ]
            ],
            [
                111,
                [
                    'version' => 1,
                    'data' => [
                        'name' => 'Foo ltd',
                        'companyNumber' => [
                            'company_number' => '123456789'
                        ]
                    ]
                ],
                [
                    ['foo' => 'bar'],
                    ['foo' => 'cake']
                ],
                123,
                [
                    'id' => 111,
                    'version' => 1,
                    'natureOfBusinesses' => [
                        ['foo' => 'bar'],
                        ['foo' => 'cake']
                    ],
                    'companyOrLlpNo' => '123456789',
                    'name' => 'Foo ltd',
                    'contactDetails' => 123
                ]
            ]
        ];
    }
}

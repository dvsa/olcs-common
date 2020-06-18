<?php

/**
 * Test Address view helper
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */

namespace CommonTest\View\Helper;

use \Common\View\Helper\Address;

/**
 * Test Address view helper
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class AddressTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the view helper
     */
    public function setUp(): void
    {
        $this->viewHelper = new Address();
    }

    /**
     * Test invoke
     * @dataProvider addressDataProvider
     */
    public function testInvokeDefaultFields($input, $expected)
    {
        if (!empty($input['fields'])) {
            // specified fields to return
            $this->assertEquals($expected, $this->viewHelper->__invoke($input['address'], $input['fields']));
        } else {
            // use default
            $this->assertEquals($expected, $this->viewHelper->__invoke($input['address']));
        }
    }

    public function addressDataProvider()
    {
        return [
            [ // include countryCode
                [
                    'address' => [
                        'addressLine1' => 'a1',
                        'addressLine2' => 'a2',
                        'addressLine3' => 'a3',
                        'town' => 't',
                        'postcode' => 'pc',
                        'countryCode' => ['id' => 'cc']
                    ]
                ],
                'a1, a2, a3, t, pc, cc'
            ],
            [ // no country code
                [
                    'address' => [
                        'addressLine1' => 'a1',
                        'addressLine2' => 'a2',
                        'addressLine3' => 'a3',
                        'town' => 't',
                        'postcode' => 'pc'
                    ],
                    'fields' => null
                 ],
                'a1, a2, a3, t, pc'
            ],
            [ // include select fields
                [
                    'address' => [
                        'addressLine1' => 'a1',
                        'addressLine2' => 'a2',
                        'addressLine3' => 'a3',
                        'town' => 't',
                        'postcode' => 'pc',
                        'countryCode' => ['id' => 'cc']
                    ],
                    'fields' => [
                        'addressLine1',
                        'addressLine3',
                        'postcode',
                        'countryCode'
                    ]
                ],
                'a1, a3, pc, cc'
            ],
        ];
    }
}

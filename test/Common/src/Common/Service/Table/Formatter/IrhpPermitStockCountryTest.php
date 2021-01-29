<?php

/**
 * Irhp Permit Stock Country formatter test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\IrhpPermitStockCountry;

class IrhpPermitStockCountryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test the format method
     *
     * @group Formatters
     *
     * @dataProvider dpFormat
     */
    public function testFormat($data, $expected)
    {
        $this->assertEquals(
            $expected,
            IrhpPermitStockCountry::format($data)
        );
    }

    public function dpFormat()
    {
        return [
            'No country' => [
                [],
                'N/A',
            ],
            'Country only' => [
                [
                    'country' => [
                        'countryDesc' => 'Bosnia & Herzegovina'
                    ]
                ],
                'Bosnia &amp; Herzegovina',
            ],
            'Country and permit category' => [
                [
                    'country' => [
                        'countryDesc' => 'Bosnia & Herzegovina'
                    ],
                    'permitCategory' => [
                        'description' => 'Hors contingent'
                    ]
                ],
                'Bosnia &amp; Herzegovina Hors contingent',
            ],
        ];
    }
}

<?php

/**
 * Constrained countries list test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\ConstrainedCountriesList;

class ConstrainedCountriesListTest extends \PHPUnit\Framework\TestCase
{
    public function testFormat()
    {
        $data = [
            'constrainedCountries' => [
                [
                    'countryDesc' => 'United Kingdom'
                ],
                [
                    'countryDesc' => 'Trinidad & Tobago'
                ],
                [
                    'countryDesc' => '"Third" country'
                ],
            ]
        ];

        $this->assertEquals(
            'United Kingdom, Trinidad &amp; Tobago, &quot;Third&quot; country',
            ConstrainedCountriesList::format($data, [])
        );
    }

    public function testFormatEmptyData()
    {
        $data = [
            'constrainedCountries' => []
        ];

        $this->assertEquals(
            'No exclusions',
            ConstrainedCountriesList::format($data, [])
        );
    }
}

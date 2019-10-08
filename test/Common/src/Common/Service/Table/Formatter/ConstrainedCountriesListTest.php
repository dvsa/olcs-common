<?php

/**
 * Constrained countries list test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\ConstrainedCountriesList;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConstrainedCountriesListTest extends MockeryTestCase
{
    private $sm;

    public function setUp()
    {
        $this->sm = m::mock(ServiceLocatorInterface::class);
        $this->sm->allows('get->translate')
            ->andReturnUsing(
                function ($key) {
                    return '_TRNSLT_' . $key;
                }
            );
    }

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
            '_TRNSLT_United Kingdom, _TRNSLT_Trinidad &amp; Tobago, _TRNSLT_&quot;Third&quot; country',
            ConstrainedCountriesList::format($data, [], $this->sm)
        );
    }

    public function testFormatWithColumnName()
    {
        $columnName = 'anyName';

        $data = [
            $columnName => [
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
            '_TRNSLT_United Kingdom, _TRNSLT_Trinidad &amp; Tobago, _TRNSLT_&quot;Third&quot; country',
            ConstrainedCountriesList::format($data, ['name' => $columnName], $this->sm)
        );
    }

    public function testFormatEmptyData()
    {
        $data = [
            'constrainedCountries' => []
        ];

        $this->assertEquals(
            '_TRNSLT_no.constrained.countries',
            ConstrainedCountriesList::format($data, [], $this->sm)
        );
    }
}

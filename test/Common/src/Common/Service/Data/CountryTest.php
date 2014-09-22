<?php

namespace CommonTest\Service\Data;

use Common\Service\Data\Country;

/**
 * Class Country Test
 * @package CommonTest\Service
 */
class CountryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetServiceName()
    {
        $sut = new Country();
        $this->assertEquals('Country', $sut->getServiceName());
    }

    public function testFormatData()
    {
        $source = $this->getSingleSource();
        $expected = $this->getSingleExpected();

        $sut = new Country();

        $this->assertEquals($expected, $sut->formatData($source['Results']));
    }

    /**
     * @return array
     */
    protected function getSingleExpected()
    {
        $expected = [
            'val-1' => 'Value 1',
            'val-2' => 'Value 2',
            'val-3' => 'Value 3',
        ];
        return $expected;
    }

    /**
     * @return array
     */
    protected function getSingleSource()
    {
        $source = [
            'Results' => [
                ['id' => 'val-1', 'countryDesc' => 'Value 1'],
                ['id' => 'val-2', 'countryDesc' => 'Value 2'],
                ['id' => 'val-3', 'countryDesc' => 'Value 3'],
            ]
        ];
        return $source;
    }
}

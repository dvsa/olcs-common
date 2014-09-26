<?php

/**
 * Data Map Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Helper;

use PHPUnit_Framework_TestCase;
use Common\Service\Helper\DataMapHelperService;

/**
 * Data Map Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DataMapHelperServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Service\Helper\DataMapHelperService
     */
    private $sut;

    /**
     * Setup the sut
     */
    protected function setUp()
    {
        $this->sut = new DataMapHelperService();
    }

    /**
     * @group helper_service
     * @group data_map_helper_service
     */
    public function testProcessDataMapForSaveWithoutMap()
    {
        $input = array(
            'foo' => 'bar'
        );

        $output = $this->sut->processDataMapForSave($input);

        $this->assertEquals($input, $output);
    }

    /**
     * @group helper_service
     * @group data_map_helper_service
     */
    public function testProcessDataMapForSave()
    {
        $input = array(
            'foo' => array(
                'a' => 'A',
                'b' => 'B'
            ),
            'bar' => array(
                'c' => 'C',
                'd' => 'D'
            ),
            'bob' => array(
                'e' => 'E',
                'f' => 'F'
            )
        );

        $map = array(
            'main' => array(
                'mapFrom' => array('foo', 'bar'),
                'values' => array('cake' => 'cats'),
                'children' => array(
                    'bobs' => array(
                        'mapFrom' => array('bob')
                    )
                )
            )
        );

        $expected = array(
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
            'd' => 'D',
            'cake' => 'cats',
            'bobs' => array(
                'e' => 'E',
                'f' => 'F'
            )
        );

        $output = $this->sut->processDataMapForSave($input, $map);

        $this->assertEquals($expected, $output);
    }

    /**
     * @group helper_service
     * @group data_map_helper_service
     */
    public function testProcessDataMapForSaveWithAddress()
    {
        $input = array(
            'foo' => array(
                'a' => 'A',
                'b' => 'B'
            ),
            'bar' => array(
                'c' => 'C',
                'd' => 'D'
            ),
            'someAddress' => array(
                'addressLine1' => '123',
                'addressLine2' => '456',
                'searchPostcode' => 'foo',
                'countryCode' => 'uk'
            )
        );

        $map = array(
            '_addresses' => array(
                'someAddress'
            ),
            'main' => array(
                'mapFrom' => array('foo', 'bar', 'addresses'),
            )
        );

        $expected = array(
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
            'd' => 'D',
            'someAddress' => array(
                'addressLine1' => '123',
                'addressLine2' => '456',
                'countryCode' => 'uk'
            )
        );

        $output = $this->sut->processDataMapForSave($input, $map);

        $this->assertEquals($expected, $output);
    }
}

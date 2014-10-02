<?php

/**
 * Data Map Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Helper;

use PHPUnit_Framework_TestCase;
use Common\Service\Helper\DataHelperService;

/**
 * Data Map Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DataHelperServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Service\Helper\DataHelperService
     */
    private $sut;

    /**
     * Setup the sut
     */
    protected function setUp()
    {
        $this->sut = new DataHelperService();
    }

    /**
     * @group helper_service
     * @group data_map_helper_service
     */
    public function testProcessDataMapWithoutMap()
    {
        $input = array(
            'foo' => 'bar'
        );

        $output = $this->sut->processDataMap($input);

        $this->assertEquals($input, $output);
    }

    /**
     * @group helper_service
     * @group data_map_helper_service
     */
    public function testProcessDataMap()
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

        $output = $this->sut->processDataMap($input, $map);

        $this->assertEquals($expected, $output);
    }

    /**
     * @group helper_service
     * @group data_map_helper_service
     */
    public function testProcessDataMapWithAddress()
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

        $output = $this->sut->processDataMap($input, $map);

        $this->assertEquals($expected, $output);
    }
}

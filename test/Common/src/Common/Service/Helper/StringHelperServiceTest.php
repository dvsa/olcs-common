<?php

/**
 * String Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Helper;

use PHPUnit_Framework_TestCase;
use Common\Service\Helper\StringHelperService;

/**
 * String Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class StringHelperServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Service\Helper\StringHelperService
     */
    private $sut;

    /**
     * Setup the sut
     */
    protected function setUp()
    {
        $this->sut = new StringHelperService();
    }

    /**
     * @dataProvider provider
     * @group helper_service
     * @group string_helper_service
     */
    public function testDashToCamel($dash, $camel)
    {
        $this->assertEquals($camel, $this->sut->dashToCamel($dash));
    }

    /**
     * @dataProvider provider
     * @group helper_service
     * @group string_helper_service
     */
    public function testCamelToDash($dash, $camel)
    {
        $this->assertEquals($dash, $this->sut->camelToDash($camel));
    }

    public function provider()
    {
        return array(
            array(
                'this-that',
                'ThisThat'
            ),
            array(
                'foo-bar-baz',
                'FooBarBaz'
            ),
            array(
                'foo',
                'Foo'
            ),
            array(
                'foo cake this-that',
                'Foo cake thisThat'
            )
        );
    }
}

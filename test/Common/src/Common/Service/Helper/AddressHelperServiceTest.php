<?php

/**
 * Test the address service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Helper;

use PHPUnit_Framework_TestCase;
use Common\Service\Helper\AddressHelperService;

/**
 * Test the address service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AddressHelperServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Holds the service
     *
     * @var object
     */
    private $service;

    /**
     * Setup the service
     */
    public function setUp()
    {
        $this->service = new AddressHelperService();
    }

    /**
     * Test formatPostalAddress with simple parts
     *
     * @group helper_service
     * @group address_helper_service
     */
    public function testFormatPostalAddressWithSimpleParts()
    {
        $address = array(
            'address_line1' => 'My Company Ltd',
            'address_line2' => 'Awesome House Street Name',
            'address_line3' => '',
            'address_line4' => '',
            'post_town' => 'Some Town',
            'postcode' => 'AB1 1AB',
        );

        $expectedAddress = array(
            'addressLine1' => 'My Company Ltd',
            'addressLine2' => 'Awesome House Street Name',
            'addressLine3' => '',
            'addressLine4' => '',
            'town' => 'Some Town',
            'postcode' => 'AB1 1AB',
            'countryCode' => 'GB'
        );

        $addressDetails = $this->service->formatPostalAddress($address);

        $this->assertEquals($expectedAddress, $addressDetails);
    }

    /**
     * Test format addresses for select
     *
     * @group helper_service
     * @group address_helper_service
     */
    public function testFormatAddressesForSelect()
    {
        $list = array(
            array(
                'uprn' => 123,
                'address_line1' => 'My Company Ltd',
                'address_line2' => '123 Really Awesome House Street Name',
                'address_line3' => '',
                'address_line4' => '',
                'post_town' => 'Some Town',
                'postcode' => 'AB1 1AB',
            ),
            array(
                'uprn' => 234,
                'address_line1' => 'My Company Ltd',
                'address_line2' => '234 Awesome House Street Name',
                'address_line3' => '',
                'address_line4' => '',
                'post_town' => 'Some Town',
                'postcode' => 'AB1 1AB',
            ),
            array(
                'uprn' => 345,
                'address_line1' => 'My Company Ltd',
                'address_line2' => '345 Awesome House Street Name',
                'address_line3' => '',
                'address_line4' => '',
                'post_town' => 'Some Town',
                'postcode' => 'AB1 1AB',
            )
        );

        $expectedResult = array(
            123 => 'My Company Ltd, 123 Really Awesome House Street N…',
            234 => 'My Company Ltd, 234 Awesome House Street Name, So…',
            345 => 'My Company Ltd, 345 Awesome House Street Name, So…',
        );

        $result = $this->service->formatAddressesForSelect($list);

        $this->assertSame($expectedResult, $result);
    }
}

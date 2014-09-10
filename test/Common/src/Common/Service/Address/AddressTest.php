<?php

/**
 * Test the address service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Service\Address;

use PHPUnit_Framework_TestCase;
use Common\Service\Address\Address;

/**
 * Test the address service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AddressTest extends PHPUnit_Framework_TestCase
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
        $this->service = new Address();
    }

    /**
     * Test setters and getters
     */
    public function testSetGetAddress()
    {
        $address = array(
            'foo' => 'bar',
            'cake' => null
        );

        $this->service->setAddress($address);

        $this->assertEquals($address, $this->service->getAddress());

        $this->assertEquals('bar', $this->service->getAddressPart('foo'));

        $this->assertEquals('', $this->service->getAddressPart('cake'));

        $this->assertEquals('', $this->service->getAddressPart('missing'));
    }

    /**
     * Test formatPostalAddressFromBs7666 with simple parts
     */
    public function testFormatPostalAddressFromBs7666WithSimpleParts()
    {
        $address = array(
            'organisation_name' => 'My Company Ltd',
            'building_name' => 'Awesome House',
            'street_description' => 'STREET NAME',
            'locality_name' => null,
            'town_name' => 'Some Town',
            'administritive_area' => 'Some Area',
            'postcode' => 'AB1 1AB'
        );

        $expectedAddress = array(
            'addressLine1' => 'My Company Ltd',
            'addressLine2' => 'Awesome House Street Name',
            'addressLine3' => '',
            'addressLine4' => '',
            'town' => 'Some Town',
            'postcode' => 'AB1 1AB'
        );

        $addressDetails = $this->service->formatPostalAddressFromBs7666($address);

        $this->assertEquals($expectedAddress, $addressDetails);
    }

    /**
     * Test formatPostalAddressFromBs7666
     */
    public function testFormatPostalAddressFromBs7666()
    {
        $address = array(
            'sao_start_number' => '1',
            'sao_start_prefix' => 'a',
            'sao_end_number' => '22',
            'sao_end_suffix' => 'b',
            'sao_text' => 'Awesome house',
            'pao_start_number' => '31',
            'pao_start_prefix' => '',
            'pao_end_number' => '',
            'pao_end_suffix' => '',
            'pao_text' => 'Some Street',
            'street_description' => 'STREET NAME',
            'locality_name' => null,
            'town_name' => 'Some Town',
            'administritive_area' => 'Some Area',
            'postcode' => 'AB1 1AB'
        );

        $expectedAddress = array(
            'addressLine1' => '1a-22b Awesome House',
            'addressLine2' => '31 Some Street Street Name',
            'addressLine3' => '',
            'addressLine4' => '',
            'town' => 'Some Town',
            'postcode' => 'AB1 1AB'
        );

        $addressDetails = $this->service->formatPostalAddressFromBs7666($address);

        $this->assertEquals($expectedAddress, $addressDetails);
    }

    /**
     * Test formatPostalAddressFromBs7666 With admin area same as town_name
     */
    public function testFormatPostalAddressFromBs7666WithSameAdminAndTown()
    {
        $address = array(
            'sao_start_number' => '1',
            'sao_start_prefix' => 'a',
            'sao_end_number' => '22',
            'sao_end_suffix' => 'b',
            'sao_text' => 'Awesome house',
            'pao_start_number' => '31',
            'pao_start_prefix' => '',
            'pao_end_number' => '',
            'pao_end_suffix' => '',
            'pao_text' => 'Some Street',
            'street_description' => 'STREET NAME',
            'locality_name' => null,
            'town_name' => 'Some Town',
            'administritive_area' => 'Some Town',
            'postcode' => 'AB1 1AB'
        );

        $expectedAddress = array(
            'addressLine1' => '1a-22b Awesome House',
            'addressLine2' => '31 Some Street Street Name',
            'addressLine3' => '',
            'addressLine4' => '',
            'town' => 'Some Town',
            'postcode' => 'AB1 1AB'
        );

        $addressDetails = $this->service->formatPostalAddressFromBs7666($address);

        $this->assertEquals($expectedAddress, $addressDetails);
    }

    /**
     * Test formatPostalAddressFromBs7666 With pre-defined address
     */
    public function testFormatPostalAddressFromBs7666WithPreDefinedAddress()
    {
        $address = array(
            'sao_start_number' => '1',
            'sao_start_prefix' => 'a',
            'sao_end_number' => '22',
            'sao_end_suffix' => 'b',
            'sao_text' => 'Awesome house',
            'pao_start_number' => '31',
            'pao_start_prefix' => '',
            'pao_end_number' => '',
            'pao_end_suffix' => '',
            'pao_text' => 'Some Street',
            'street_description' => 'STREET NAME',
            'locality_name' => null,
            'town_name' => 'Some Town',
            'administritive_area' => 'Some Town',
            'postcode' => 'AB1 1AB'
        );

        $expectedAddress = array(
            'addressLine1' => '1a-22b Awesome House',
            'addressLine2' => '31 Some Street Street Name',
            'addressLine3' => '',
            'addressLine4' => '',
            'town' => 'Some Town',
            'postcode' => 'AB1 1AB'
        );

        $this->service->setAddress($address);

        $addressDetails = $this->service->formatPostalAddressFromBs7666();

        $this->assertEquals($expectedAddress, $addressDetails);
    }

    /**
     * Test format addresses for select
     */
    public function testFormatAddressesForSelect()
    {
        $list = array(
            array(
                'uprn' => 123,
                'organisation_name' => 'My Company Ltd',
                'building_name' => 'Awesome House',
                'street_description' => 'STREET NAME',
                'locality_name' => null,
                'town_name' => 'Some Town',
                'administritive_area' => 'Some Area',
                'postcode' => 'AB1 1AB'
            ),
            array(
                'uprn' => 234,
                'sao_start_number' => '1',
                'sao_start_prefix' => 'a',
                'sao_end_number' => '22',
                'sao_end_suffix' => 'b',
                'sao_text' => 'Awesome house',
                'pao_start_number' => '31',
                'pao_start_prefix' => '',
                'pao_end_number' => '',
                'pao_end_suffix' => '',
                'pao_text' => 'Some Street',
                'street_description' => 'STREET NAME',
                'locality_name' => null,
                'town_name' => 'Some Town',
                'administritive_area' => 'Some Area',
                'postcode' => 'AB1 1AB'
            ),
            array(
                'uprn' => 345,
                'sao_start_number' => '1',
                'sao_start_prefix' => 'a',
                'sao_end_number' => '22',
                'sao_end_suffix' => 'b',
                'sao_text' => 'Awesome house',
                'pao_start_number' => '31',
                'pao_start_prefix' => '',
                'pao_end_number' => '',
                'pao_end_suffix' => '',
                'pao_text' => 'Some Street',
                'street_description' => 'STREET NAME',
                'locality_name' => null,
                'town_name' => 'Some Town',
                'administritive_area' => 'Some Town',
                'postcode' => 'AB1 1AB'
            )
        );

        $expectedResult = array(
            123 => 'My Company Ltd, Awesome House Street Name, Some Town',
            234 => '1a-22b Awesome House, 31 Some Street Street Name, Some Town',
            345 => '1a-22b Awesome House, 31 Some Street Street Name, Some Town'
        );

        $result = $this->service->formatAddressesForSelect($list);

        $this->assertEquals($expectedResult, $result);
    }
}

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
     * Test formatPostalAddressFromBs7666 with simple parts
     *
     * @group helper_service
     * @group address_helper_service
     */
    public function testFormatPostalAddressFromBs7666WithSimpleParts()
    {
        $address = array(
            'organisation_name' => 'My Company Ltd',
            'building_name' => 'Awesome House',
            'street_description' => 'STREET NAME',
            'locality_name' => null,
            'post_town' => 'Some Town',
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
     *
     * @group helper_service
     * @group address_helper_service
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
            'post_town' => 'Some Town',
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
     * Test formatPostalAddressFromBs7666 With admin area same as post_town
     *
     * @group helper_service
     * @group address_helper_service
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
            'post_town' => 'Some Town',
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
                'organisation_name' => 'My Company Ltd',
                'building_name' => 'Awesome House',
                'street_description' => 'STREET NAME',
                'locality_name' => null,
                'post_town' => 'Some Town',
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
                'post_town' => 'Some Town',
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
                'post_town' => 'Some Town',
                'administritive_area' => 'Some Town',
                'postcode' => 'AB1 1AB'
            )
        );

        $expectedResult = array(
            123 => 'My Company Ltd, Awesome House Street Name, Some T…',
            234 => '1a-22b Awesome House, 31 Some Street Street Name,…',
            345 => '1a-22b Awesome House, 31 Some Street Street Name,…'
        );

        $result = $this->service->formatAddressesForSelect($list);

        $this->assertEquals($expectedResult, $result);
    }
}

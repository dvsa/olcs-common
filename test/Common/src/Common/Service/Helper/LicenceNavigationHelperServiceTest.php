<?php

/**
 * Licence Details Helper Tests
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Helper;

use PHPUnit_Framework_TestCase;
use Common\Service\Helper\LicenceNavigationHelperService;
use Common\Controller\Service\LicenceSectionService;

/**
 * Licence Details Helper Tests
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceNavigationHelperServiceTest extends PHPUnit_Framework_TestCase
{
    private $helper;

    public function setUp()
    {
        $restrictionHelper = new \Common\Service\Helper\RestrictionHelperService();

        $mockFactory = $this->getMock('\Common\Service\Helper\HelperServiceFactory', array('getHelperService'));
        $mockFactory->expects($this->any())
            ->method('getHelperService')
            ->will($this->returnValue($restrictionHelper));

        $this->helper = new LicenceNavigationHelperService();
        $this->helper->setHelperServiceFactory($mockFactory);
    }

    /**
     * Test the getAccessibleSections returns the correct sections for the licence
     *
     * @dataProvider getAccessibleSectionsProvider
     * @group helper_service
     * @group licence_navigation_helper_service
     */
    public function testGetAccessibleSections($goodsOrPsv, $licenceType, $expected)
    {
        $actual = $this->helper->getAccessibleSections($goodsOrPsv, $licenceType);

        $this->assertEquals($expected, array_keys($actual));
    }

    /**
     * Test the doesLicenceHaveAccess returns the correct access rights
     *
     * @dataProvider doesLicenceHaveAccessProvider
     * @group helper_service
     * @group licence_navigation_helper_service
     */
    public function testDoesLicenceHaveAccess($section, $goodsOrPsv, $licenceType, $expected)
    {
        $actual = $this->helper->doesLicenceHaveAccess($section, $goodsOrPsv, $licenceType);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test get navigation
     *
     * @group helper_service
     * @group licence_navigation_helper_service
     */
    public function testGetNavigation()
    {
        $licenceId = 1;
        $goodsOrPsv = LicenceSectionService::LICENCE_CATEGORY_GOODS_VEHICLE;
        $licenceType = LicenceSectionService::LICENCE_TYPE_STANDARD_NATIONAL;
        $activeSection = 'overview';

        $nav = $this->helper->getNavigation($licenceId, $goodsOrPsv, $licenceType, $activeSection);

        // We should have 10 sections
        $this->assertEquals(10, count($nav));

        // First sectiion (overview) should be active
        $this->assertTrue($nav[0]['active']);
        $this->assertFalse($nav[1]['active']);

        // Assert that the licence route param is set
        $this->assertEquals($licenceId, $nav[0]['params']['licence']);
    }

    public function getAccessibleSectionsProvider()
    {
        return array(
            array(
                LicenceSectionService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceSectionService::LICENCE_TYPE_RESTRICTED,
                array(
                    'overview',
                    'type_of_licence',
                    'business_details',
                    'address',
                    'people',
                    'operating_centre',
                    'vehicle',
                    'safety',
                    'condition_undertaking'
                )
            ),
            array(
                LicenceSectionService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceSectionService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                array(
                    'overview',
                    'type_of_licence',
                    'business_details',
                    'address',
                    'people',
                    'operating_centre',
                    'transport_manager',
                    'vehicle',
                    'safety',
                    'condition_undertaking'
                )
            ),
            array(
                LicenceSectionService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceSectionService::LICENCE_TYPE_STANDARD_NATIONAL,
                array(
                    'overview',
                    'type_of_licence',
                    'business_details',
                    'address',
                    'people',
                    'operating_centre',
                    'transport_manager',
                    'vehicle',
                    'safety',
                    'condition_undertaking'
                )
            ),
            array(
                LicenceSectionService::LICENCE_CATEGORY_PSV,
                LicenceSectionService::LICENCE_TYPE_RESTRICTED,
                array(
                    'overview',
                    'type_of_licence',
                    'business_details',
                    'address',
                    'people',
                    'operating_centre',
                    'vehicle_psv',
                    'safety',
                    'condition_undertaking'
                )
            ),
            array(
                LicenceSectionService::LICENCE_CATEGORY_PSV,
                LicenceSectionService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                array(
                    'overview',
                    'type_of_licence',
                    'business_details',
                    'address',
                    'people',
                    'operating_centre',
                    'transport_manager',
                    'vehicle_psv',
                    'safety',
                    'condition_undertaking'
                )
            ),
            array(
                LicenceSectionService::LICENCE_CATEGORY_PSV,
                LicenceSectionService::LICENCE_TYPE_STANDARD_NATIONAL,
                array(
                    'overview',
                    'type_of_licence',
                    'business_details',
                    'address',
                    'people',
                    'operating_centre',
                    'transport_manager',
                    'vehicle_psv',
                    'safety',
                    'condition_undertaking'
                )
            ),
            array(
                LicenceSectionService::LICENCE_CATEGORY_PSV,
                LicenceSectionService::LICENCE_TYPE_SPECIAL_RESTRICTED,
                array(
                    'overview',
                    'type_of_licence',
                    'business_details',
                    'address',
                    'people',
                    'taxi_phv'
                )
            )
        );
    }

    /**
     * @NOTE Here I just test a selected group of scenarios, the above test will cover all scenarios anyway
     *
     * @return array
     */
    public function doesLicenceHaveAccessProvider()
    {
        return array(
            // Test that every licence can see overview
            array(
                'overview',
                LicenceSectionService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceSectionService::LICENCE_TYPE_RESTRICTED,
                true
            ),
            array(
                'overview',
                LicenceSectionService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceSectionService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                true
            ),
            array(
                'overview',
                LicenceSectionService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceSectionService::LICENCE_TYPE_STANDARD_NATIONAL,
                true
            ),
            array(
                'overview',
                LicenceSectionService::LICENCE_CATEGORY_PSV,
                LicenceSectionService::LICENCE_TYPE_RESTRICTED,
                true
            ),
            array(
                'overview',
                LicenceSectionService::LICENCE_CATEGORY_PSV,
                LicenceSectionService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                true
            ),
            array(
                'overview',
                LicenceSectionService::LICENCE_CATEGORY_PSV,
                LicenceSectionService::LICENCE_TYPE_STANDARD_NATIONAL,
                true
            ),
            array(
                'overview',
                LicenceSectionService::LICENCE_CATEGORY_PSV,
                LicenceSectionService::LICENCE_TYPE_SPECIAL_RESTRICTED,
                true
            ),
            // Test which licence has taxi phv
            array(
                'taxi_phv',
                LicenceSectionService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceSectionService::LICENCE_TYPE_RESTRICTED,
                false
            ),
            array(
                'taxi_phv',
                LicenceSectionService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceSectionService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                false
            ),
            array(
                'taxi_phv',
                LicenceSectionService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceSectionService::LICENCE_TYPE_STANDARD_NATIONAL,
                false
            ),
            array(
                'taxi_phv',
                LicenceSectionService::LICENCE_CATEGORY_PSV,
                LicenceSectionService::LICENCE_TYPE_RESTRICTED,
                false
            ),
            array(
                'taxi_phv',
                LicenceSectionService::LICENCE_CATEGORY_PSV,
                LicenceSectionService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                false
            ),
            array(
                'taxi_phv',
                LicenceSectionService::LICENCE_CATEGORY_PSV,
                LicenceSectionService::LICENCE_TYPE_STANDARD_NATIONAL,
                false
            ),
            array(
                'taxi_phv',
                LicenceSectionService::LICENCE_CATEGORY_PSV,
                LicenceSectionService::LICENCE_TYPE_SPECIAL_RESTRICTED,
                true
            ),
            // Test who can transport manager
            array(
                'transport_manager',
                LicenceSectionService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceSectionService::LICENCE_TYPE_RESTRICTED,
                false
            ),
            array(
                'transport_manager',
                LicenceSectionService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceSectionService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                true
            ),
            array(
                'transport_manager',
                LicenceSectionService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceSectionService::LICENCE_TYPE_STANDARD_NATIONAL,
                true
            ),
            array(
                'transport_manager',
                LicenceSectionService::LICENCE_CATEGORY_PSV,
                LicenceSectionService::LICENCE_TYPE_RESTRICTED,
                false
            ),
            array(
                'transport_manager',
                LicenceSectionService::LICENCE_CATEGORY_PSV,
                LicenceSectionService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                true
            ),
            array(
                'transport_manager',
                LicenceSectionService::LICENCE_CATEGORY_PSV,
                LicenceSectionService::LICENCE_TYPE_STANDARD_NATIONAL,
                true
            ),
            array(
                'transport_manager',
                LicenceSectionService::LICENCE_CATEGORY_PSV,
                LicenceSectionService::LICENCE_TYPE_SPECIAL_RESTRICTED,
                false
            )
        );
    }
}

<?php

/**
 * Licence Navigation Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

use Common\Controller\Service\LicenceSectionService;

/**
 * Licence Navigation Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceNavigationHelperService extends AbstractHelperService
{
    /**
     * Holds the section config
     *
     * @var array
     */
    protected $sections = array(
        'overview' => array(

        ),
        'type_of_licence' => array(

        ),
        'business_details' => array(

        ),
        'address' => array(

        ),
        'people' => array(

        ),
        'operating_centre' => array(
            'restricted' => array(
                // @NOTE The licence must follow ANY of these restrictions
                LicenceSectionService::LICENCE_TYPE_RESTRICTED,
                LicenceSectionService::LICENCE_TYPE_STANDARD_NATIONAL,
                LicenceSectionService::LICENCE_TYPE_STANDARD_INTERNATIONAL
            )
        ),
        'transport_manager' => array(
            'restricted' => array(
                LicenceSectionService::LICENCE_TYPE_STANDARD_NATIONAL,
                LicenceSectionService::LICENCE_TYPE_STANDARD_INTERNATIONAL
            )
        ),
        'vehicle' => array(
            'restricted' => array(
                array(
                    LicenceSectionService::LICENCE_CATEGORY_GOODS_VEHICLE,
                    array(
                        LicenceSectionService::LICENCE_TYPE_RESTRICTED,
                        LicenceSectionService::LICENCE_TYPE_STANDARD_NATIONAL,
                        LicenceSectionService::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
            )
        ),
        'vehicle_psv' => array(
            'restricted' => array(
                array(
                    LicenceSectionService::LICENCE_CATEGORY_PSV,
                    array(
                        LicenceSectionService::LICENCE_TYPE_RESTRICTED,
                        LicenceSectionService::LICENCE_TYPE_STANDARD_NATIONAL,
                        LicenceSectionService::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
            )
        ),
        'safety' => array(
            'restricted' => array(
                LicenceSectionService::LICENCE_TYPE_RESTRICTED,
                LicenceSectionService::LICENCE_TYPE_STANDARD_NATIONAL,
                LicenceSectionService::LICENCE_TYPE_STANDARD_INTERNATIONAL
            )
        ),
        'condition_undertaking' => array(
            'restricted' => array(
                LicenceSectionService::LICENCE_TYPE_RESTRICTED,
                LicenceSectionService::LICENCE_TYPE_STANDARD_NATIONAL,
                LicenceSectionService::LICENCE_TYPE_STANDARD_INTERNATIONAL
            )
        ),
        'taxi_phv' => array(
            'restricted' => array(
                // @NOTE The licence must follow ALL of these restrictions
                array(
                    LicenceSectionService::LICENCE_CATEGORY_PSV,
                    LicenceSectionService::LICENCE_TYPE_SPECIAL_RESTRICTED
                )
            )
        )
    );

    /**
     * Get a list of accessible sections
     *
     * @param string $goodsOrPsv
     * @param string $licenceType
     * @return array
     */
    public function getAccessibleSections($goodsOrPsv, $licenceType)
    {
        $sections = $this->sections;

        foreach (array_keys($sections) as $section) {
            if (!$this->doesLicenceHaveAccess($section, $goodsOrPsv, $licenceType)) {
                unset($sections[$section]);
            }
        }

        return $sections;
    }

    /**
     * Check if the licence has access to the section
     *
     * @param string $section
     * @param string $goodsOrPsv
     * @param string $licenceType
     * @return boolean
     */
    public function doesLicenceHaveAccess($section, $goodsOrPsv, $licenceType)
    {
        $sectionDetails = $this->sections[$section];

        // If the section has no restrictions just return
        if (!isset($sectionDetails['restricted'])) {
            return true;
        }

        $access = array($goodsOrPsv, $licenceType);
        $restrictions = $sectionDetails['restricted'];

        return $this->getHelperService('RestrictionHelper')->isRestrictionSatisfied($restrictions, $access);
    }

    /**
     * Get navigation config
     *
     * @param int $licenceId
     * @param string $goodsOrPsv
     * @param string $licenceType
     * @return array
     */
    public function getNavigation($licenceId, $goodsOrPsv, $licenceType, $activeSection = null)
    {
        $sections = $this->getAccessibleSections($goodsOrPsv, $licenceType);

        $navigation = array();

        foreach (array_keys($sections) as $section) {
            $navigation[] = array(
                'label' => 'internal-licence-details-' . $section . '-label',
                'title' => 'internal-licence-details-' . $section . '-title',
                'route' => 'licence/details/' . $section,
                'use_route_match' => true,
                'params' => array(
                    'licence' => $licenceId
                ),
                'active' => $section == $activeSection
            );
        }

        return $navigation;
    }
}

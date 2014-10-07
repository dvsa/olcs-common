<?php

/**
 * Section Config
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Data;

use Common\Service\Entity\LicenceService;

/**
 * Section Config
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SectionConfig
{
    /**
     * Holds the section config
     *
     * @var array
     */
    private $sections = array(
        'type_of_licence' => array(),
        'business_details' => array(
            'restricted' => array(
                LicenceService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceService::LICENCE_CATEGORY_PSV
            )
        ),
        'addresses' => array(
            'restricted' => array(
                LicenceService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceService::LICENCE_CATEGORY_PSV
            )
        ),
        'people' => array(
            'restricted' => array(
                LicenceService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceService::LICENCE_CATEGORY_PSV
            )
        ),
        'taxi_phv' => array(
            'restricted' => array(
                LicenceService::LICENCE_TYPE_SPECIAL_RESTRICTED
            )
        ),
        'operating_centres' => array(
            'restricted' => array(
                LicenceService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceService::LICENCE_CATEGORY_PSV
            )
        ),
        'financial_evidence' => array(
            'restricted' => array(
                LicenceService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceService::LICENCE_CATEGORY_PSV
            )
        ),
        'transport_managers' => array(
            'restricted' => array(
                LicenceService::LICENCE_TYPE_STANDARD_NATIONAL,
                LicenceService::LICENCE_TYPE_STANDARD_INTERNATIONAL
            )
        ),
        'vehicles' => array(
            'restricted' => array(
                LicenceService::LICENCE_CATEGORY_GOODS_VEHICLE
            )
        ),
        'vehicles_psv' => array(
            'restricted' => array(
                LicenceService::LICENCE_CATEGORY_PSV
            )
        ),
        'vehicles_declarations' => array(
            'restricted' => array(
                LicenceService::LICENCE_CATEGORY_PSV
            )
        ),
        'discs' => array(
            'restricted' => array(
                LicenceService::LICENCE_CATEGORY_PSV
            )
        ),
        'community_licences' => array(
            'restricted' => array(
                LicenceService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                array(
                    LicenceService::LICENCE_CATEGORY_PSV,
                    LicenceService::LICENCE_TYPE_RESTRICTED
                )
            )
        ),
        'safety' => array(
            'restricted' => array(
                LicenceService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceService::LICENCE_CATEGORY_PSV
            )
        ),
        'conditions_undertakings' => array(
            'restricted' => array(
                LicenceService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceService::LICENCE_CATEGORY_PSV
            )
        ),
        'financial_history' => array(
            'restricted' => array(
                LicenceService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceService::LICENCE_CATEGORY_PSV
            )
        ),
        'licence_history' => array(
            'restricted' => array(
                LicenceService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceService::LICENCE_CATEGORY_PSV
            )
        ),
        'convictions_penalties' => array(
            'restricted' => array(
                LicenceService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceService::LICENCE_CATEGORY_PSV
            )
        )
    );

    /**
     * Return all sections
     *
     * @return array
     */
    public function getAll()
    {
        return $this->sections;
    }

    /**
     * Return all section references
     *
     * @return array
     */
    public function getAllReferences()
    {
        return array_keys($this->sections);
    }
}

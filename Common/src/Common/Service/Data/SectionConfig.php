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
        'business_details' => array(),
        'addresses' => array(),
        'people' => array(),
        'taxi_phv' => array(
            'restricted' => array(
                LicenceService::LICENCE_TYPE_SPECIAL_RESTRICTED
            )
        ),
        'operating_centres' => array(),
        'financial_evidence' => array(),
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
        'safety' => array(),
        'conditions_undertakings' => array(),
        'financial_history' => array(),
        'licence_history' => array(),
        'convictions_penalties' => array()
    );

    /**
     * Return all sections
     *
     * @return array;
     */
    public function getAll()
    {
        return $this->sections;
    }
}

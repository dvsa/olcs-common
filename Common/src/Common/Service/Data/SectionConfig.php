<?php

/**
 * Section Config
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Data;

use Common\Service\Entity\LicenceService;
use Zend\Filter\Word\UnderscoreToDash;
use Zend\Filter\Word\UnderscoreToCamelCase;

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
        'business_type' => array(),
        'business_details' => array(
            'prerequisite' => array(
                'business_type'
            )
        ),
        'addresses' => array(
            'prerequisite' => array(
                'business_type'
            )
        ),
        'people' => array(
            'prerequisite' => array(
                'business_type'
            )
        ),
        'taxi_phv' => array(
            'restricted' => array(
                LicenceService::LICENCE_TYPE_SPECIAL_RESTRICTED
            )
        ),
        'operating_centres' => array(
            'restricted' => array(
                LicenceService::LICENCE_TYPE_RESTRICTED,
                LicenceService::LICENCE_TYPE_STANDARD_NATIONAL,
                LicenceService::LICENCE_TYPE_STANDARD_INTERNATIONAL
            )
        ),
        'financial_evidence' => array(
            'restricted' => array(
                array(
                    array(
                        'application'
                    ),
                    array(
                        LicenceService::LICENCE_TYPE_RESTRICTED,
                        LicenceService::LICENCE_TYPE_STANDARD_NATIONAL,
                        LicenceService::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
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
                array(
                    LicenceService::LICENCE_CATEGORY_GOODS_VEHICLE,
                    array(
                        LicenceService::LICENCE_TYPE_RESTRICTED,
                        LicenceService::LICENCE_TYPE_STANDARD_NATIONAL,
                        LicenceService::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
            )
        ),
        'vehicles_psv' => array(
            'restricted' => array(
                array(
                    LicenceService::LICENCE_CATEGORY_PSV,
                    array(
                        LicenceService::LICENCE_TYPE_RESTRICTED,
                        LicenceService::LICENCE_TYPE_STANDARD_NATIONAL,
                        LicenceService::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
            )
        ),
        'vehicles_declarations' => array(
            'restricted' => array(
                array(
                    'application',
                    LicenceService::LICENCE_CATEGORY_PSV,
                    array(
                        LicenceService::LICENCE_TYPE_RESTRICTED,
                        LicenceService::LICENCE_TYPE_STANDARD_NATIONAL,
                        LicenceService::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
            )
        ),
        'discs' => array(
            'restricted' => array(
                array(
                    array(
                        'licence',
                        'variation'
                    ),
                    LicenceService::LICENCE_CATEGORY_PSV,
                    array(
                        LicenceService::LICENCE_TYPE_RESTRICTED,
                        LicenceService::LICENCE_TYPE_STANDARD_NATIONAL,
                        LicenceService::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
            )
        ),
        'community_licences' => array(
            'restricted' => array(
                array(
                    // Must be variation or licence
                    array(
                        'variation',
                        'licence'
                    ),
                    // and must be either
                    array(
                        // standard international
                        LicenceService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                        // or
                        array(
                            // PSV
                            LicenceService::LICENCE_CATEGORY_PSV,
                            // and restricted
                            LicenceService::LICENCE_TYPE_RESTRICTED
                        )
                    )
                )
            )
        ),
        'safety' => array(
            'restricted' => array(
                LicenceService::LICENCE_TYPE_RESTRICTED,
                LicenceService::LICENCE_TYPE_STANDARD_NATIONAL,
                LicenceService::LICENCE_TYPE_STANDARD_INTERNATIONAL
            )
        ),
        'conditions_undertakings' => array(
            'restricted' => array(
                array(
                    array(
                        LicenceService::LICENCE_TYPE_RESTRICTED,
                        LicenceService::LICENCE_TYPE_STANDARD_NATIONAL,
                        LicenceService::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    ),
                    array(
                        'internal',
                        'licence',
                        'variation'
                    )
                )
            )
        ),
        'financial_history' => array(
            'restricted' => array(
                array(
                    'application',
                    array(
                        LicenceService::LICENCE_TYPE_RESTRICTED,
                        LicenceService::LICENCE_TYPE_STANDARD_NATIONAL,
                        LicenceService::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
            )
        ),
        'licence_history' => array(
            'restricted' => array(
                array(
                    'application',
                    array(
                        LicenceService::LICENCE_TYPE_RESTRICTED,
                        LicenceService::LICENCE_TYPE_STANDARD_NATIONAL,
                        LicenceService::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
            )
        ),
        'convictions_penalties' => array(
            'restricted' => array(
                array(
                    'application',
                    array(
                        LicenceService::LICENCE_TYPE_RESTRICTED,
                        LicenceService::LICENCE_TYPE_STANDARD_NATIONAL,
                        LicenceService::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
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

    /**
     * Return route config for all sections
     *
     * @return array
     */
    public function getAllRoutes()
    {
        $sections = $this->getAllReferences();

        $dashFilter = new UnderscoreToDash();
        $camelFilter = new UnderscoreToCamelCase();

        $types = array(
            'application' => array(),
            'licence' => array(),
            'variation' => array()
        );

        $routes = array();

        foreach ($types as $type => $options) {
            $typeController = 'Lva' . $camelFilter->filter($type);
            $baseRouteName = 'lva-' . $type;

            $routes[$baseRouteName] = array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/' . $type . '/:id[/]',
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => $typeController,
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array()
            );

            $childRoutes = array();
            foreach ($sections as $section) {
                $routeKey = $dashFilter->filter($section);
                $sectionController = $camelFilter($section);

                $childRoutes[$section] = array(
                    'type' => 'segment',
                    'options' => array(
                        'route' => $routeKey . '[/:action][/]',
                        'defaults' => array(
                            'controller' => $typeController . '/' . $sectionController,
                            'action' => 'index'
                        )
                    )
                );
            }
            $routes[$baseRouteName]['child_routes'] = $childRoutes;
        }

        return $routes;
    }
}

<?php

namespace Common\Service\Data;

use Common\RefData;
use Laminas\Filter\Word\UnderscoreToDash;
use Laminas\Filter\Word\UnderscoreToCamelCase;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Section Config
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SectionConfig implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Holds the section config
     *
     * @var array
     */
    private $sections = array(
        'type_of_licence' => array(),

        'business_type' => array(
            'prerequisite' => array(
                'type_of_licence'
            )
        ),
        'business_details' => array(
            'prerequisite' => array(
                array(
                    'type_of_licence',
                    'business_type'
                )
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
                RefData::LICENCE_TYPE_SPECIAL_RESTRICTED
            )
        ),
        'operating_centres' => array(
            'restricted' => array(
                RefData::LICENCE_TYPE_RESTRICTED,
                RefData::LICENCE_TYPE_STANDARD_NATIONAL,
                RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
            )
        ),
        'financial_evidence' => array(
            'prerequisite' => array(
                'operating_centres'
            ),
            'restricted' => array(
                array(
                    array(
                        'application'
                    ),
                    array(
                        RefData::LICENCE_TYPE_RESTRICTED,
                        RefData::LICENCE_TYPE_STANDARD_NATIONAL,
                        RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
            )
        ),
        'transport_managers' => array(
            'restricted' => array(
                RefData::LICENCE_TYPE_STANDARD_NATIONAL,
                RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
            )
        ),
        'vehicles' => array(
            'restricted' => array(
                array(
                    RefData::LICENCE_CATEGORY_GOODS_VEHICLE,
                    array(
                        RefData::LICENCE_TYPE_RESTRICTED,
                        RefData::LICENCE_TYPE_STANDARD_NATIONAL,
                        RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
            )
        ),
        'vehicles_psv' => array(
            'prerequisite' => array(
                'operating_centres'
            ),
            'restricted' => array(
                array(
                    RefData::LICENCE_CATEGORY_PSV,
                    array(
                        RefData::LICENCE_TYPE_RESTRICTED,
                        RefData::LICENCE_TYPE_STANDARD_NATIONAL,
                        RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
            )
        ),
        'vehicles_declarations' => array(
            'prerequisite' => array(
                'operating_centres'
            ),
            'restricted' => array(
                array(
                    'application',
                    RefData::LICENCE_CATEGORY_PSV,
                    array(
                        RefData::LICENCE_TYPE_RESTRICTED,
                        RefData::LICENCE_TYPE_STANDARD_NATIONAL,
                        RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
            )
        ),
        'trailers' => array(
            'restricted' => array(
                array(
                    'external',
                    'licence',
                    RefData::LICENCE_CATEGORY_GOODS_VEHICLE
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
                    RefData::LICENCE_CATEGORY_PSV,
                    array(
                        RefData::LICENCE_TYPE_RESTRICTED,
                        RefData::LICENCE_TYPE_STANDARD_NATIONAL,
                        RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
            )
        ),
        'community_licences' => array(
            'restricted' => array(
                array(
                    // Only shown internally
                    array(
                        'internal'
                    ),
                    // and must be either
                    array(
                        // standard international
                        RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                        // or
                        array(
                            // PSV
                            RefData::LICENCE_CATEGORY_PSV,
                            // and restricted
                            RefData::LICENCE_TYPE_RESTRICTED
                        )
                    )
                )
            )
        ),
        'safety' => array(
            'restricted' => array(
                RefData::LICENCE_TYPE_RESTRICTED,
                RefData::LICENCE_TYPE_STANDARD_NATIONAL,
                RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
            )
        ),
        'conditions_undertakings' => array(
            'restricted' => array(
                array(
                    // Must be one of these licence types
                    array(
                        RefData::LICENCE_TYPE_RESTRICTED,
                        RefData::LICENCE_TYPE_STANDARD_NATIONAL,
                        RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    ),
                    // and...
                    array(
                        // either internal
                        'internal',
                        // or...
                        array(
                            // external
                            'external',
                            // with conditions to show
                            'hasConditions',
                            // for licences
                            'licence',
                        )
                    )
                )
            )
        ),
        'financial_history' => array(
            'restricted' => array(
                array(
                    'application',
                    array(
                        RefData::LICENCE_TYPE_RESTRICTED,
                        RefData::LICENCE_TYPE_STANDARD_NATIONAL,
                        RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
            )
        ),
        'licence_history' => array(
            'restricted' => array(
                array(
                    'application',
                    array(
                        RefData::LICENCE_TYPE_RESTRICTED,
                        RefData::LICENCE_TYPE_STANDARD_NATIONAL,
                        RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
            )
        ),
        'convictions_penalties' => array(
            'restricted' => array(
                array(
                    'application',
                    array(
                        RefData::LICENCE_TYPE_RESTRICTED,
                        RefData::LICENCE_TYPE_STANDARD_NATIONAL,
                        RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
            )
        ),

        // external decalrations
        'undertakings' => [
            'restricted' => [
                [
                    // Must be variation or application
                    [
                        'application',
                        'variation'
                    ],
                    [
                        'external'
                    ],
                ]
            ],
        ],
        'declarations_internal' => [
            'restricted' => [
                [
                    // Must be variation or application
                    [
                        'application',
                        'variation'
                    ],
                    [
                        'internal'
                    ],
                ]
            ],
        ],
    );

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
            'application' => array(
                'identifier' => 'application'
            ),
            'licence' => array(
                'identifier' => 'licence'
            ),
            'variation' => array(
                'identifier' => 'application'
            ),
            'director_change' => array(
                'identifier' => 'application'
            ),
        );

        $routes = array();

        foreach ($types as $type => $options) {
            $typeController = 'Lva' . $camelFilter->filter($type);
            $baseRouteName = 'lva-' . $type;

            $routes[$baseRouteName] = array(
                'type' => 'segment',
                'options' => array(
                    'route' => sprintf('/%s/:%s[/]', $dashFilter->filter($type), $options['identifier']),
                    'constraints' => array(
                        $options['identifier'] => '[0-9]+'
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

                $childRoutes[$section] = [
                    'type' => \Common\Util\LvaRoute::class,
                    'options' => [
                        'route' => $routeKey . '[/]',
                        'defaults' => [
                            'controller' => $typeController . '/' . $sectionController,
                            'action' => 'index',
                        ],
                    ],
                    'may_terminate' => true,
                    'child_routes' => [
                        'action' => [
                            'type' => \Laminas\Mvc\Router\Http\Segment::class,
                            'options' => [
                                'route' => ':action[/:child_id][/]',
                            ],
                        ],
                    ],
                ];
            }
            $routes[$baseRouteName]['child_routes'] = $childRoutes;
        }

        return $routes;
    }
}

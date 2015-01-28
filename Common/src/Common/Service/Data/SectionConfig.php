<?php

/**
 * Section Config
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Data;

use Common\Service\Entity\LicenceEntityService;
use Zend\Filter\Word\UnderscoreToDash;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

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
                LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED
            )
        ),
        'operating_centres' => array(
            'restricted' => array(
                LicenceEntityService::LICENCE_TYPE_RESTRICTED,
                LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
            )
        ),
        'financial_evidence' => array(
            'restricted' => array(
                array(
                    array(
                        'application'
                    ),
                    array(
                        LicenceEntityService::LICENCE_TYPE_RESTRICTED,
                        LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                        LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
            )
        ),
        'transport_managers' => array(
            'restricted' => array(
                LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
            )
        ),
        'vehicles' => array(
            'restricted' => array(
                array(
                    LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE,
                    array(
                        LicenceEntityService::LICENCE_TYPE_RESTRICTED,
                        LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                        LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
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
                    LicenceEntityService::LICENCE_CATEGORY_PSV,
                    array(
                        LicenceEntityService::LICENCE_TYPE_RESTRICTED,
                        LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                        LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
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
                    LicenceEntityService::LICENCE_CATEGORY_PSV,
                    array(
                        LicenceEntityService::LICENCE_TYPE_RESTRICTED,
                        LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                        LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
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
                    LicenceEntityService::LICENCE_CATEGORY_PSV,
                    array(
                        LicenceEntityService::LICENCE_TYPE_RESTRICTED,
                        LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                        LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
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
                        LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                        // or
                        array(
                            // PSV
                            LicenceEntityService::LICENCE_CATEGORY_PSV,
                            // and restricted
                            LicenceEntityService::LICENCE_TYPE_RESTRICTED
                        )
                    )
                )
            )
        ),
        'safety' => array(
            'restricted' => array(
                LicenceEntityService::LICENCE_TYPE_RESTRICTED,
                LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
            )
        ),
        'conditions_undertakings' => array(
            'restricted' => array(
                array(
                    array(
                        LicenceEntityService::LICENCE_TYPE_RESTRICTED,
                        LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                        LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
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
                        LicenceEntityService::LICENCE_TYPE_RESTRICTED,
                        LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                        LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
            )
        ),
        'licence_history' => array(
            'restricted' => array(
                array(
                    'application',
                    array(
                        LicenceEntityService::LICENCE_TYPE_RESTRICTED,
                        LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                        LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
            )
        ),
        'convictions_penalties' => array(
            'restricted' => array(
                array(
                    'application',
                    array(
                        LicenceEntityService::LICENCE_TYPE_RESTRICTED,
                        LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                        LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
                    )
                )
            )
        ),
        'undertakings' => array(
            'restricted' => array(
                array(
                    // Must be variation or application
                    array(
                        'application',
                        'variation'
                    ),
                    // and external
                    array(
                        'external',
                    )
                )
            ),
        ),
    );

    protected $init = false;

    protected function initSections()
    {
        if ($this->init === false) {

            $this->sections['financial_history']['restricted'][] = array(
                'variation',
                array($this->getServiceLocator()->get('Processing\VariationSection'), 'isNotUnchanged')
            );

            $this->sections['convictions_penalties']['restricted'][] = array(
                'variation',
                array($this->getServiceLocator()->get('Processing\VariationSection'), 'isNotUnchanged')
            );

            $this->sections['financial_evidence']['restricted'][] = array(
                'variation',
                array($this->getServiceLocator()->get('Processing\VariationSection'), 'isNotUnchanged')
            );

            $this->sections['vehicles_declarations']['restricted'][] = array(
                'variation',
                array($this->getServiceLocator()->get('Processing\VariationSection'), 'isNotUnchanged')
            );

            // undertakings requires all sections (except itself)
            $this->sections['undertakings']['prerequisite'] = $this->getAllReferences();
            $key = array_search('undertakings', $this->sections['undertakings']['prerequisite']);
            unset($this->sections['undertakings']['prerequisite'][$key]);
        }
    }

    /**
     * Return all sections
     *
     * @return array
     */
    public function getAll()
    {
        $this->initSections();

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
            'application' => array(
                'identifier' => 'application'
            ),
            'licence' => array(
                'identifier' => 'licence'
            ),
            'variation' => array(
                'identifier' => 'application'
            )
        );

        $routes = array();

        foreach ($types as $type => $options) {
            $typeController = 'Lva' . $camelFilter->filter($type);
            $baseRouteName = 'lva-' . $type;

            $routes[$baseRouteName] = array(
                'type' => 'segment',
                'options' => array(
                    'route' => sprintf('/%s/:%s[/]', $type, $options['identifier']),
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

                $childRoutes[$section] = array(
                    'type' => 'segment',
                    'options' => array(
                        'route' => $routeKey . '[/:action[/:child_id]][/]',
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

<?php

/**
 * Abstract LVA Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Abstract LVA Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractLvaEntityService extends AbstractEntityService
{
    /**
     * Operating Centres bundle
     */
    protected $ocBundle = array(
        'properties' => array(
            'id',
            'version',
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles',
            'totCommunityLicences',
            'totAuthVehicles',
            'totAuthTrailers',
        ),
        'children' => array(
            'licence' => array(
                'properties' => array(
                    'id'
                ),
                'children' => array(
                    'trafficArea' => array(
                        'properties' => array(
                            'id',
                            'name'
                        )
                    )
                )
            ),
            'operatingCentre' => array(
                'properties' => array(
                    'id',
                    'version'
                ),
                'children' => array(
                    'address' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'addressLine1',
                            'addressLine2',
                            'addressLine3',
                            'addressLine4',
                            'postcode',
                            'town'
                        ),
                        'children' => array(
                            'countryCode' => array(
                                'properties' => array('id')
                            )
                        )
                    ),
                    'adDocuments' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'filename',
                            'identifier',
                            'size'
                        )
                    )
                )
            )
        )
    );

    /**
     * Document Bundle
     *
     * @var array
     */
    protected $documentBundle = array(
        'properties' => array(),
        'children' => array(
            'documents' => array(
                'properties' => array(
                    'id',
                    'version',
                    'filename',
                    'identifier',
                    'size',
                    'category',
                    'documentSubCategory'
                ),
                // granted, not everything needs this, but it saves another bundle
                'children' => array(
                    'operatingCentre' => array(
                        'properties' => array('id')
                    ),
                    'category' => array(
                        'properties' => array('id')
                    ),
                    'documentSubCategory' => array(
                        'properties' => array('id')
                    )
                )
            )
        )
    );

    protected $totalAuthorisationBundle = array(
        'properties' => array(
            'totAuthVehicles',
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles'
        )
    );

    /**
     * Get operating centres data
     *
     * @param int $id
     * @return array
     */
    public function getOperatingCentresData($id)
    {
        return $this->get($id, $this->ocBundle);
    }

    public function getDocuments($id, $categoryName, $documentSubCategoryName)
    {
        $documentBundle = $this->documentBundle;

        $categoryService = $this->getServiceLocator()->get('category');

        $category = $categoryService->getCategoryByDescription($categoryName);
        $subCategory = $categoryService->getCategoryByDescription($documentSubCategoryName, 'Document');

        $documentBundle['children']['documents']['criteria'] = array(
            '_OPTIONS_' => array(
                // @NOTE: doctrine is borking on these particular queries; go manual instead
                'manualSearch' => true,
            ),
            'category' => $category['id'],
            'documentSubCategory' => $subCategory['id']
        );

        $data = $this->get($id, $documentBundle);

        return $data['documents'];
    }

    public function getTotalVehicleAuthorisation($id, $type = '')
    {
        return $this->get($id, $this->totalAuthorisationBundle)['totAuth' . $type . 'Vehicles'];
    }
}

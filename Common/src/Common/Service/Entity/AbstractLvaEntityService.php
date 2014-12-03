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
    protected $ocBundle = array();

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
     * Vehicle PSV bundle
     *
     * @var array
     */
    protected $vehiclesPsvBundle = array(
        'properties' => array(
            'id',
            'version',
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles',
            'hasEnteredReg'
        )
    );

    /**
     * Holds the bundle to retrieve type of licence bundle
     *
     * @var array
     */
    protected $typeOfLicenceBundle = array(
        'children' => array(
            'goodsOrPsv',
            'licenceType'
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

    public function getDataForVehiclesPsv($id)
    {
        return $this->get($id, $this->vehiclesPsvBundle);
    }

    /**
     * Get type of licence data
     *
     * @param int $id
     * @return array
     */
    public function getTypeOfLicenceData($id)
    {
        $data = $this->get($id, $this->typeOfLicenceBundle);

        return array(
            'version' => $data['version'],
            'niFlag' => $data['niFlag'],
            'licenceType' => isset($data['licenceType']['id']) ? $data['licenceType']['id'] : null,
            'goodsOrPsv' => isset($data['goodsOrPsv']['id']) ? $data['goodsOrPsv']['id'] : null
        );
    }
}

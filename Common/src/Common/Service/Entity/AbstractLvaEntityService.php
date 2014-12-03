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
        'children' => array(
            'licence' => array(
                'children' => array(
                    'trafficArea'
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
        'children' => array(
            'documents' => array(
                // granted, not everything needs this, but it saves another bundle
                'children' => array(
                    'operatingCentre',
                    'category',
                    'documentSubCategory'
                )
            )
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
        return $this->get($id)['totAuth' . $type . 'Vehicles'];
    }

    public function getDataForVehiclesPsv($id)
    {
        return $this->get($id);
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

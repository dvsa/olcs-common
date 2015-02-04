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
                    'subCategory'
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
        // @NOTE: we're going to be consolidating calls which
        // no longer need to define a bundle
        return $this->get($id);
    }

    public function getDocuments($id, $categoryId, $documentSubCategoryId)
    {
        $documentBundle = $this->documentBundle;

        $documentBundle['children']['documents']['criteria'] = array(
            'category'    => $categoryId,
            'subCategory' => $documentSubCategoryId
        );

        $data = $this->get($id, $documentBundle);

        return $data['documents'];
    }

    public function getTotalVehicleAuthorisation($id, $type = '')
    {
        return $this->get($id)['totAuth' . $type . 'Vehicles'];
    }

    abstract public function getTotalVehicleAuthorisationIncLicence($id, $type = '');

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
        if (!$this->isCached('typeOfLicence', $id)) {

            $data = $this->get($id, $this->typeOfLicenceBundle);

            $this->setCache(
                'typeOfLicence',
                $id,
                array(
                    'version' => $data['version'],
                    'niFlag' => $data['niFlag'],
                    'licenceType' => isset($data['licenceType']['id']) ? $data['licenceType']['id'] : null,
                    'goodsOrPsv' => isset($data['goodsOrPsv']['id']) ? $data['goodsOrPsv']['id'] : null
                )
            );
        }

        return $this->getCache('typeOfLicence', $id);
    }
}

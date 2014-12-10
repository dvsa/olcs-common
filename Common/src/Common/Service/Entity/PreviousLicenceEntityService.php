<?php

/**
 * Previous Licence Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Previous Licence Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PreviousLicenceEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'PreviousLicence';

    protected $licenceTableBundle = array(
        'children' => array(
            'previousLicenceType'
        )
    );

    /**
     * Holds the data bundle
     *
     * @var array
     */
    protected $dataBundle = array(
        'children' => array(
            'previousLicenceType'
        )
    );

    public function getForApplicationAndType($applicationId, $prevLicenceType)
    {
        $data = $this->getAll(
            array('application' => $applicationId, 'previousLicenceType' => $prevLicenceType),
            $this->licenceTableBundle
        );

        return $data['Results'];
    }

    public function getById($id)
    {
        return $this->get($id, $this->dataBundle);
    }
}

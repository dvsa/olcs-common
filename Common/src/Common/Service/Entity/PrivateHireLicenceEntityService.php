<?php

/**
 * Private Hire Licence Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Private Hire Licence Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PrivateHireLicenceEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'PrivateHireLicence';

    protected $dataBundle = array(
        'properties' => array(
            'id',
            'version',
            'privateHireLicenceNo',
        ),
        'children' => array(
            'contactDetails' => array(
                'properties' => array(
                    'id',
                    'version',
                    'description'
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
                                'properties' => array(
                                    'id'
                                )
                            )
                        )
                    )
                )
            )
        )
    );

    protected $countBundle = array(
        'properties' => array(
            'id'
        )
    );

    public function getByLicenceId($licenceId)
    {
        return $this->get(array('licence' => $licenceId), $this->dataBundle)['Results'];
    }

    public function getCountByLicence($licenceId)
    {
        return $this->get(array('licence' => $licenceId), $this->countBundle)['Count'];
    }

    public function getById($id)
    {
        return $this->get($id, $this->dataBundle);
    }
}

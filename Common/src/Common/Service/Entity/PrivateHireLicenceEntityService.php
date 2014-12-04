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
        'children' => array(
            'contactDetails' => array(
                'children' => array(
                    'address' => array(
                        'children' => array(
                            'countryCode'
                        )
                    )
                )
            )
        )
    );

    public function getByLicenceId($licenceId)
    {
        return $this->get(array('licence' => $licenceId), $this->dataBundle)['Results'];
    }

    public function getCountByLicence($licenceId)
    {
        return $this->get(array('licence' => $licenceId))['Count'];
    }

    public function getById($id)
    {
        return $this->get($id, $this->dataBundle);
    }
}

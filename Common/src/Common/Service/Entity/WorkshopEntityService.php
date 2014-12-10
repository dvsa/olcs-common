<?php

/**
 * Workshop Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Workshop Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class WorkshopEntityService extends AbstractEntityService
{
    protected $entity = 'Workshop';

    private $workshopDataBundle = array(
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

    public function getForLicence($licenceId)
    {
        $results = $this->getAll(['licence' => $licenceId], $this->workshopDataBundle);

        return $results['Results'];
    }

    public function getById($id)
    {
        return $this->get($id, $this->workshopDataBundle);
    }
}

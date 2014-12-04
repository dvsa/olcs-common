<?php

/**
 * Application Operating Centre Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Application Operating Centre Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ApplicationOperatingCentreEntityService extends AbstractOperatingCentreEntityService
{
    protected $entity = 'ApplicationOperatingCentre';

    protected $type = 'application';

    protected $dataBundle = array(
        'children' => array(
            'operatingCentre'
        )
    );

    public function getForApplication($id)
    {
        $query = array('application' => $id);

        $results = $this->getAll($query, $this->dataBundle);

        return $results['Results'];
    }
}

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
        'properties' => array(
            'id',
            'version',
            'licNo',
            'holderName',
            'willSurrender',
            'purchaseDate',
            'disqualificationDate',
            'disqualificationLength'
        ),
        'children' => array(
            'previousLicenceType' => array(
                'properties' => array('id')
            )
        )
    );

    public function getForApplicationAndType($applicationId, $prevLicenceType)
    {
        $data = $this->get(
            array('application' => $applicationId, 'previousLicenceType' => $prevLicenceType),
            $this->licenceTableBundle
        );

        return $data['Results'];
    }
}

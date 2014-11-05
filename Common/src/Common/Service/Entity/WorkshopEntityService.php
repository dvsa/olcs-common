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

    private $licenceWorkshopBundle = array(
        'properties' => array(
            'id',
            'isExternal'
        ),
        'children' => array(
            'contactDetails' => array(
                'properties' => array(
                    'fao'
                ),
                'children' => array(
                    'address' => array(
                        'properties' => array(
                            'addressLine1',
                            'addressLine2',
                            'addressLine3',
                            'addressLine4',
                            'town',
                            'postcode'
                        ),
                        'children' => array(
                            'countryCode' => array(
                                'properties' => array('id')
                            )
                        )
                    )
                )
            )
        )
    );

    private $workshopDataBundle = array(
        'properties' => array(
            'version',
            'isExternal'
        ),
        'children' => array(
            'contactDetails' => array(
                'properties' => array(
                    'id',
                    'version',
                    'fao'
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
                            'town',
                            'postcode'
                        ),
                        'children' => array(
                            'countryCode' => array(
                                'properties' => array('id')
                            )
                        )
                    )
                )
            )
        )
    );

    public function getForLicence($licenceId)
    {
        $results = $this->get(['licence' => $licenceId], $this->licenceWorkshopBundle);

        return $results['Results'];
    }

    public function getById($id)
    {
        return $this->get($id, $this->workshopDataBundle);
    }
}

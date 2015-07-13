<?php

/**
 * Licence Operating Centre Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Licence Operating Centre Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LicenceOperatingCentreEntityService extends AbstractOperatingCentreEntityService
{
    protected $entity = 'LicenceOperatingCentre';

    protected $type = 'licence';

    protected $authorityDataBundle = array(
        'children' => array(
            'operatingCentre'
        )
    );

    protected $operatingCentresForLicenceBundle = [
        'children' => [
            'operatingCentre' => [
                'children' => [
                    'address'
                ]
            ]
        ]
    ];

    public function getVehicleAuths($id)
    {
        return $this->get($id);
    }

    public function variationDelete($id, $applicationId)
    {
        $addressData = $this->getAddressData($id);
        $addressData['action'] = 'D';
        $addressData['application'] = $applicationId;

        unset($addressData['id']);
        unset($addressData['version']);
        unset($addressData['createdOn']);
        unset($addressData['lastModifiedOn']);

        $addressData['operatingCentre'] = $addressData['operatingCentre']['id'];

        return $this->getServiceLocator()->get('Entity\ApplicationOperatingCentre')->save($addressData);
    }

    public function getOperatingCentresForLicence($licId)
    {
        return $this->getAll(['licence' => $licId], $this->operatingCentresForLicenceBundle);
    }

    public function getAuthorityDataForLicence($licId)
    {
        $data = $this->get(['licence' => $licId], $this->authorityDataBundle);

        return $data['Results'];
    }
}

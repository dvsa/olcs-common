<?php

/**
 * Application Operating Centre Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

use Common\Service\Table\Formatter\Address;

/**
 * Application Operating Centre Entity Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationOperatingCentreEntityService extends AbstractOperatingCentreEntityService
{
    protected $entity = 'ApplicationOperatingCentre';

    protected $type = 'application';

    protected $dataBundle = [
        'children' => [
            'operatingCentre'
        ]
    ];

    protected $selectBundle = [
        'children' => [
            'application',
            'operatingCentre' => [
                'children' => [
                    'address'
                ]
            ]
        ]
    ];

    public function getForApplication($id)
    {
        $query = ['application' => $id];

        $results = $this->getAll($query, $this->dataBundle);

        return $results['Results'];
    }

    public function getByApplicationAndOperatingCentre($applicationId, $operatingCentreId)
    {
        return $this->get(['application' => $applicationId, 'operatingCentre' => $operatingCentreId])['Results'];
    }

    /**
     * Clear all interim markers against a set of application OCs
     * @todo migrated (maybe remove?)
     */
    public function clearInterims(array $ids = [])
    {
        $data = [];

        foreach ($ids as $id) {

            $data[] = [
                'id' => $id,
                'isInterim' => false,
                '_OPTIONS_' => ['force' => true]
            ];
        }

        $data['_OPTIONS_']['multiple'] = true;

        $this->put($data);
    }

    /**
     * Get all OC for given application for inspection request listbox
     *
     * @param int $applicationId
     * @return array
     */
    public function getAllForInspectionRequest($applicationId)
    {
        $query = [
            'application' => $applicationId,
            'action' => '!= D'
        ];
        $bundle = [
            'children' => [
                'operatingCentre' => [
                    'children' => [
                        'address'
                    ]
                ],
                'application'
            ]
        ];
        return $this->getAll($query, $bundle);
    }

    public function getForSelect($applicationId)
    {
        $data = $this->getAll(['application' => $applicationId], $this->selectBundle);

        $list = [];
        $deleted = [];

        $addressFields = ['addressLine1', 'town'];
        $options = ['name' => 'address', 'addressFields' => $addressFields];

        foreach ($data['Results'] as $result) {

            $id = $result['operatingCentre']['id'];

            if ($result['action'] !== 'D') {
                $list[$id] = Address::format($result['operatingCentre'], $options);
            } else {
                $deleted[] = $id;
            }
        }

        $licenceId = $this->getServiceLocator()->get('Entity\Application')->getLicenceIdForApplication($applicationId);

        $licenceOcData = $this->getServiceLocator()->get('Entity\LicenceOperatingCentre')
            ->getOperatingCentresForLicence($licenceId);

        foreach ($licenceOcData['Results'] as $result) {

            $id = $result['operatingCentre']['id'];

            if (!in_array($id, $deleted)) {
                $list[$id] = Address::format($result['operatingCentre'], $options);
            }
        }

        return $list;
    }
}

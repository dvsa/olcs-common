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
}

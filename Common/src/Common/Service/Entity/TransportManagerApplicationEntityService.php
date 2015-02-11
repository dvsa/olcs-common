<?php

/**
 * Transport Manager Application Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Transport Manager Application Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TransportManagerApplicationEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'TransportManagerApplication';

    protected $dataBundle = [
        'children' => [
            'application' => [
                'children' => [
                    'status',
                    'licence' => [
                        'children' => [
                            'organisation'
                        ]
                    ]
                ]
            ],
            'tmApplicationStatus',
            'transportManager',
            'tmType',
            'operatingCentres'
        ]
    ];

    protected $grantDataBundle = [
        'children' => [
            'application',
            'tmApplicationStatus',
            'transportManager',
            'tmType',
            'operatingCentres'
        ]
    ];

    public function getGrantDataForApplication($applicationId)
    {
        return $this->getAll(['application' => $applicationId], $this->grantDataBundle)['Results'];
    }

    /**
     * Get transport manager applications
     *
     * @param int $id
     * @param array $status
     * @return array
     */
    public function getTransportManagerApplications($id, $status = [])
    {
        $results = $this->get(['transportManager' => $id], $this->dataBundle);

        $finalResults = [];

        foreach ($results['Results'] as &$result) {

            if (in_array($result['application']['status']['id'], $status)) {
                $result['ocCount'] = count($result['operatingCentres']);
                $finalResults[] = $result;
            }
        }

        return $results;
    }

    /**
     * Get transport manager application
     *
     * @param int $id
     * @return array
     */
    public function getTransportManagerApplication($id)
    {
        return $this->get($id, $this->dataBundle);
    }

    public function getByApplication($applicationId)
    {
        return $this->get(['application' => $applicationId]);
    }
}

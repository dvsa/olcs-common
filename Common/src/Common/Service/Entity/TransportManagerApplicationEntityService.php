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
            'tmApplicationOcs' => [
                'children' => [
                    'operatingCentre'
                ]
             ]
        ]
    ];

    protected $grantDataBundle = [
        'children' => [
            'application',
            'tmApplicationStatus',
            'transportManager',
            'tmType',
            'tmApplicationOcs' => [
                'children' => [
                    'operatingCentre'
                ]
             ]
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
        $query = [
            'transportManager' => $id,
            'action' => '!= D'
        ];
        if (count($status)) {
            $query['tmApplicationStatus'] = $status;
        }

        $results = $this->get($query, $this->dataBundle);

        foreach ($results['Results'] as &$result) {
            $result['ocCount'] = count($result['tmApplicationOcs']);
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

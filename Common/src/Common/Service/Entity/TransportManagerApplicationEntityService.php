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
            'tmApplicationOcs'
        ]
    ];

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
            'transportManagerId' => $id,
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
}

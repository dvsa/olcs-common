<?php

/**
 * Transport Manager Licence Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Transport Manager Licence Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TransportManagerLicenceEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'TransportManagerLicence';

    protected $dataBundle = [
        'children' => [
            'licence' => [
                'children' => [
                    'organisation',
                    'status'
                ]
            ],
            'transportManager' => [
                'children' => [
                    'tmType'
                ]
            ],
            'tmType',
            'tmLicenceOcs' => [
                'children' => [
                    'operatingCentre'
                ]
             ]
        ]
    ];

    /**
     * Get transport manager licences
     *
     * @param int $id
     * @param array $status
     * @return array
     */
    public function getTransportManagerLicences($id, $status = [])
    {
        $query = [
            'transportManagerId' => $id,
        ];
        $finalResults = [];
        $results = $this->get($query, $this->dataBundle);
        foreach ($results['Results'] as $result) {
            if (in_array($result['licence']['status']['id'], $status)) {
                $result['ocCount'] = count($result['tmLicenceOcs']);
                $finalResults[] = $result;
            }
        }
        return $finalResults;
    }

    /**
     * Get transport manager licence
     *
     * @param int $id
     * @return array
     */
    public function getTransportManagerLicence($id)
    {
        return $this->get($id, $this->dataBundle);
    }
}

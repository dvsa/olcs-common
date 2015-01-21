<?php

/**
 * Transport Manager Application Operating Centre Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Transport Manager Application Operating Centre Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TmApplicationOperatingCentreEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'TmApplicationOc';

    protected $dataBundle = [
        'children' => [
            'transportManagerApplication',
            'operatingCentre'
        ]
    ];

    /**
     * Get all transport manager OCs for given application
     *
     * @param int $id
     * @return array
     */
    public function getAllForTmApplication($id)
    {
        return $this->get(['transportManagerApplication' => $id], $this->dataBundle);
    }

    /**
     * Delete by transport manager application id and operating centre ids
     *
     * @param int $tmAppIds
     * @param array $ocIds
     */
    public function deleteByTmAppAndIds($tmAppId, $ocIds)
    {
        if (count($ocIds)) {
            $allIds = '';
            foreach ($ocIds as $id) {
                $allIds .= '"' . $id . '", ';
            }
            $allIds = trim($allIds, ', ');
            $query = [
                'transportManagerApplication' => $tmAppId,
                'operatingCentre' => 'IN [' . $allIds . ']'
            ];
            $data = $this->getAll($query, $this->dataBundle);
            if (count($data['Results'])) {
                foreach ($data['Results'] as $record) {
                    $this->delete($record['id']);
                }
            }
        }
    }

    /**
     * Delete by transport manager application id
     *
     * @param int $id
     */
    public function deleteByTmApplication($id)
    {
        $query = [
            'transportManagerApplication' => $id,
        ];
        $data = $this->getAll($query, $this->dataBundle);
        if (count($data['Results'])) {
            foreach ($data['Results'] as $record) {
                $this->delete($record['id']);
            }
        }
    }
}

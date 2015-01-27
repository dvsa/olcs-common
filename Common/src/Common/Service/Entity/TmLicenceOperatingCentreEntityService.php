<?php

/**
 * Transport Manager Licence Operating Centre Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Transport Manager Licence Operating Centre Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TmLicenceOperatingCentreEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'TmLicenceOc';

    protected $dataBundle = [
        'children' => [
            'transportManagerLicence',
            'operatingCentre'
        ]
    ];

    /**
     * Delete by transport manager licence id
     *
     * @param int $id
     */
    public function deleteByTmLicence($id)
    {
        $query = [
            'transportManagerLicence' => $id,
        ];
        $this->deleteList($query);
    }

    /**
     * Delete by transport manager licence id and operating centre ids
     *
     * @param int $tmLicId
     * @param array $ocIds
     */
    public function deleteByTmLicAndIds($tmLicId, $ocIds)
    {
        if (count($ocIds)) {
            $allIds = '';
            foreach ($ocIds as $id) {
                $allIds .= '"' . $id . '", ';
            }
            $allIds = trim($allIds, ', ');
            $query = [
                'transportManagerLicence' => $tmLicId,
                'operatingCentre' => 'IN [' . $allIds . ']'
            ];
            $this->deleteList($query);
        }
    }

    /**
     * Get all transport manager OCs for given licence
     *
     * @param int $id
     * @return array
     */
    public function getAllForTmLicence($id)
    {
        return $this->get(['transportManagerLicence' => $id], $this->dataBundle);
    }
}

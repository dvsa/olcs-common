<?php

/**
 * Organisation Nature of Business Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Entity;

/**
 * Organisation Nature of Business Entity Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class OrganisationNatureOfBusinessEntityService extends AbstractEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'OrganisationNatureOfBusiness';

    /**
     * Data bundle
     *
     * @var array
     */
    protected $dataBundle = [
        'children' => [
            'organisation',
            'refData'
        ]
    ];

    /**
     * Get all records for given organisation
     *
     * @param int $orgId
     * @return array
     */
    public function getAllForOrganisation($orgId)
    {
        $query = [
            'organisation' => $orgId
        ];

        $data = $this->getAll($query, $this->dataBundle);

        return $data['Results'];
    }

    /**
     * Get all records for given organisation, to be used in select
     *
     * @param int $orgId
     * @return array
     */
    public function getAllForOrganisationForSelect($orgId)
    {
        $data = $this->getAllForOrganisation($orgId);

        $normalized = [];
        foreach ($data as $value) {
            $normalized[] = $value['refData']['id'];
        }
        return $normalized;
    }

    /**
     * Delete all records for all given ids and organisation
     *
     * @param array $ids
     */
    public function deleteByOrganisationAndIds($orgId, $ids = [])
    {
        if (count($ids)) {
            $allIds = '';
            foreach ($ids as $id) {
                $allIds .= '"' . $id . '", ';
            }
            $allIds = trim($allIds, ', ');
            $query = [
                'organisation' => $orgId,
                'refData' => 'IN [' . $allIds . ']'
            ];
            $data = $this->getAll($query, $this->dataBundle);
            if (count($data['Results'])) {
                foreach ($data['Results'] as $record) {
                    $this->delete($record['id']);
                }
            }
        }
    }
}

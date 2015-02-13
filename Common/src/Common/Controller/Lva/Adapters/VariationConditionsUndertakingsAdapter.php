<?php

/**
 * Variation Conditions Undertakings Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Service\Table\TableBuilder;
use Common\Controller\Lva\Adapters\AbstractOperatingCentreAdapter;
use Common\Service\Entity\ConditionUndertakingEntityService;

/**
 * Variation Conditions Undertakings Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationConditionsUndertakingsAdapter extends AbstractConditionsUndertakingsAdapter
{
    protected $tableName = 'lva-variation-conditions-undertakings';

    const ACTION_ADDED = 'A'; // Record added to the application
    const ACTION_EXISTING = 'E'; // Unchanged record against the licence
    const ACTION_CURRENT = 'C'; // Current version of record updated on the application
    const ACTION_UPDATED = 'U'; // Record updated on the application
    const ACTION_DELETED = 'D'; // Record deleted on the application
    const ACTION_REMOVED = 'R'; // The corresponding licence record, to the delta delete record

    protected $cache = [];
    protected $tableData;

    /**
     * Attach the relevant scripts to the main page
     */
    public function attachMainScripts()
    {
        $this->getServiceLocator()->get('Script')->loadFile('lva-crud-delta');
    }

    public function restore($id, $parentId)
    {
        // If we are deleting more than 1 record, it's more efficient to grab all of the table data and cache it
        // rather than making X number of rest calls
        $this->getTableData($parentId);

        $action = $this->determineAction($id, $parentId);

        switch ($action) {
            // For updated and deleted, we just delete the deltas
            case self::ACTION_UPDATED:
            case self::ACTION_DELETED:
                parent::delete($id, $parentId);
                return true;
            case self::ACTION_CURRENT:
                $data = $this->getConditionData($id, $parentId);
                parent::delete($data['variationRecords'][0]['id'], $parentId);
                return true;
        }
        return false;
    }

    /**
     * Delete a record
     *
     * @param int $id
     * @param int $parentId
     */
    public function delete($id, $parentId)
    {
        // If we are deleting more than 1 record, it's more efficient to grab all of the table data and cache it
        // rather than making X number of rest calls
        $this->getTableData($parentId);

        $action = $this->determineAction($id, $parentId);

        switch ($action) {
            // If this record is added or updated, we can just remove it
            case self::ACTION_ADDED:
            case self::ACTION_UPDATED:
                return parent::delete($id, $parentId);
            // If it is an existing one, we need to add a record to the variation
            case self::ACTION_EXISTING:
                $data = $this->cloneCondition(
                    $this->getConditionData($id, $parentId)
                );
                $data['application'] = $parentId;
                $data['action'] = self::ACTION_DELETED;
                parent::save($data);
        }
    }

    /**
     * Save the data
     *
     * @param array $data
     * @return int
     */
    public function save($data)
    {
        // If we are creating a new record, set the action to A
        if (!isset($data['id']) || empty($data['id'])) {
            $data['action'] = self::ACTION_ADDED;
            $data['addedVia'] = ConditionUndertakingEntityService::ADDED_VIA_APPLICATION;
        } else {

            $action = $this->determineAction($data['id'], $data['application']);

            if ($action === self::ACTION_EXISTING) {

                $currentData = $this->getConditionData($data['id'], $data['application']);

                $data['addedVia'] = $currentData['addedVia']['id'];

                $data = $this->cloneCondition($data);

                $data['action'] = self::ACTION_UPDATED;
            }
        }

        return parent::save($data);
    }

    /**
     * Check whether we can update the record
     *
     * @param int $id
     * @return bool
     */
    public function canEditRecord($id, $parentId)
    {
        $action = $this->determineAction($id, $parentId);

        return in_array($action, [self::ACTION_ADDED, self::ACTION_EXISTING, self::ACTION_UPDATED]);
    }

    public function determineAction($id, $parentId)
    {
        $data = $this->getConditionData($id, $parentId);

        if (isset($data['action']) && !empty($data['action'])) {
            return $data['action'];
        }

        // This covers Es
        if (empty($data['variationRecords'])) {
            return self::ACTION_EXISTING;
        }

        // This covers the Rs
        if ($data['variationRecords'][0]['action'] === 'D') {
            return self::ACTION_REMOVED;
        }

        // This covers Cs
        return self::ACTION_CURRENT;
    }

    /**
     * Get the table data
     *
     * @param int $id
     * @return array
     */
    public function getTableData($id)
    {
        if ($this->tableData === null) {
            $results = $this->getServiceLocator()->get('Entity\ConditionUndertaking')
                ->getForVariation($id);

            foreach ($results as $key => $row) {

                $this->cache[$row['id']] = $row;

                $results[$key]['action'] = $this->determineAction($row['id'], $id);

                if ($results[$key]['action'] === self::ACTION_REMOVED) {
                    unset($results[$key]);
                }
            }

            $this->tableData = $results;
        }

        return $this->tableData;
    }

    /**
     * Process the data for saving
     *
     * @param array $data
     * @param int $id
     * @return array
     */
    public function processDataForSave($data, $id)
    {
        $data = parent::processDataForSave($data, $id);

        $data['fields']['application'] = $id;
        $data['fields']['isDraft'] = 'Y';

        return $data;
    }

    /**
     * Remove the restore button
     *
     * @param TableBuilder $table
     */
    public function alterTable(TableBuilder $table)
    {

    }

    protected function cloneCondition($data)
    {
        $data['licConditionVariation'] = $data['id'];
        unset($data['id']);
        unset($data['licence']);
        unset($data['case']);

        return $data;
    }

    protected function getConditionData($id, $parentId)
    {
        if (!isset($this->cache[$id])) {
            $this->cache[$id] = $this->getServiceLocator()
                ->get('Entity\ConditionUndertaking')
                ->getConditionForVariation($id, $parentId);
        }

        return $this->cache[$id];
    }

    /**
     * Each LVA section must implement this method
     *
     * @param int id
     * @returna array
     */
    protected function getOperatingCentresForList($id)
    {
        // Grab all of the application operating centres
        $applicationOperatingCentres = $this->getLvaOperatingCentreEntityService()->getOperatingCentreListForLva($id);
        $licenceOperatingCentres = $this->getServiceLocator()->get('Entity\LicenceOperatingCentre')
            ->getOperatingCentreListForLva($this->getLicenceId($id));

        $combinedOperatingCentres = [];

        // Add all the licence operating centres to the stack, indexing them by the operating centre id
        foreach ($licenceOperatingCentres['Results'] as $loc) {
            $combinedOperatingCentres[$loc['operatingCentre']['id']] = $loc['operatingCentre'];
        }

        // Loop through the application operating centres and update the stack
        foreach ($applicationOperatingCentres['Results'] as $aoc) {

            // Add any new ocs added to the variation
            if ($aoc['action'] == AbstractOperatingCentreAdapter::ACTION_ADDED) {
                $combinedOperatingCentres[$aoc['operatingCentre']['id']] = $aoc['operatingCentre'];
            } elseif ($aoc['action'] == AbstractOperatingCentreAdapter::ACTION_DELETED) {
                // Remove any that have been deleted on the variation
                unset($combinedOperatingCentres[$aoc['operatingCentre']['id']]);
            }
        }

        return $combinedOperatingCentres;
    }

    /**
     * Get licence id from the given lva id
     *
     * @param int id
     * @return int
     */
    protected function getLicenceId($id)
    {
        return $this->getServiceLocator()->get('Entity\Application')->getLicenceIdForApplication($id);
    }

    /**
     * Get the LVA operating centre entity service
     *
     * @return \Common\Service\Entity\AbstractEntity
     */
    protected function getLvaOperatingCentreEntityService()
    {
        return $this->getServiceLocator()->get('Entity\ApplicationOperatingCentre');
    }
}

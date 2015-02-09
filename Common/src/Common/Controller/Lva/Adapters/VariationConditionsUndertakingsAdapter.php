<?php

/**
 * Variation Conditions Undertakings Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

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

    protected $cache = [];
    protected $tableData;

    /**
     * Delete a record
     *
     * @param int $id
     * @param int $parentId
     */
    public function delete($id, $parentId)
    {
        // Cache table data
        $this->getTableData($parentId);

        $action = $this->determineAction($id, $parentId);

        switch ($action) {
            case self::ACTION_DELETED:
                return;
            case self::ACTION_ADDED:
            case self::ACTION_UPDATED:
                return parent::delete($id, $parentId);
            case self::ACTION_EXISTING:
                die('here');
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

            if (!$this->canEditRecord($data['id'], $data['application'])) {
                // Shouldn't get here unless someone has tried to be naughty
                // @todo replace with other exception
                throw new \Exception('You can\'t edit this record');
            }

            $action = $this->determineAction($data['id']);

            if ($action === self::ACTION_EXISTING) {

                $data['licConditionVariation'] = $data['id'];
                unset($data['id']);
                unset($data['licence']);
                unset($data['case']);
                $data['action'] = self::ACTION_UPDATED;
                $data['addedVia'] = ConditionUndertakingEntityService::ADDED_VIA_APPLICATION;
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

        if (isset($data['action']) && !empty(isset($data['action']))) {
            return $data['action'];
        }

        // This covers Es
        if (empty($data['variationRecords'])) {
            return self::ACTION_EXISTING;
        }

        // This covers Cs
        return self::ACTION_CURRENT;
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

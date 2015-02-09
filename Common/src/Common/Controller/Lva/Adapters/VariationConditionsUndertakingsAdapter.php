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
    /**
     * Save the data
     *
     * @param array $data
     * @return int
     */
    public function save($data)
    {
        $data['addedVia'] = ConditionUndertakingEntityService::ADDED_VIA_APPLICATION;

        return parent::save($data);
    }

    /**
     * Get the table data
     *
     * @param int $id
     * @return array
     */
    public function getTableData($id)
    {
        return [];
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

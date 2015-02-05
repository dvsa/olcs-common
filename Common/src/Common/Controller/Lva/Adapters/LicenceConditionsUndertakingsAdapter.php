<?php

/**
 * Licence Conditions Undertakings Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

/**
 * Licence Conditions Undertakings Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceConditionsUndertakingsAdapter extends AbstractConditionsUndertakingsAdapter
{
    /**
     * Save the data
     *
     * @param array $data
     * @return int
     */
    public function save($data)
    {

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

        $data['fields']['licence'] = $id;

        return $data;
    }

    /**
     * Get licence id from the given lva id
     *
     * @param int id
     * @return int
     */
    protected function getLicenceId($id)
    {
        return $id;
    }

    /**
     * Get the LVA operating centre entity service
     *
     * @return \Common\Service\Entity\AbstractEntity
     */
    protected function getLvaOperatingCentreEntityService()
    {
        return $this->getServiceLocator()->get('Entity\LicenceOperatingCentre');
    }
}

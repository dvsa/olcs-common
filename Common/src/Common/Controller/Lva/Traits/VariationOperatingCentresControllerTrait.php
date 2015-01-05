<?php

/**
 * Common variation OC controller logic
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

/**
 * Common variation OC controller logic
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait VariationOperatingCentresControllerTrait
{
    protected function getDocumentProperties()
    {
        return array(
            'application' => $this->getIdentifier(),
            'licence' => $this->getLicenceId()
        );
    }

    protected function attachScripts()
    {
        $this->getServiceLocator()->get('Script')->loadFile('lva-variation-operating-centre');
    }

    protected function getTableConfigName()
    {
        return 'lva-variation-operating-centres';
    }

    /**
     * Get the entity name representing this LVA's Operating Centres
     */
    protected function getLvaOperatingCentreEntity()
    {
        return 'Entity\\ApplicationOperatingCentre';
    }

    /**
     * Get the table data for the main form
     *
     * @return array
     */
    protected function getTableData()
    {
        if (empty($this->tableData)) {

            $licenceData = $this->getIndexedTableData('Licence', $this->getLicenceId());
            $applicationData = $this->getIndexedTableData('Application', $this->getApplicationId());

            $data = $this->updateAndFilterTableData($licenceData, $applicationData);

            $this->tableData = $this->formatTableData($data);
        }

        return $this->tableData;
    }

    protected function updateAndFilterTableData($licenceData, $applicationData)
    {
        $data = array();

        foreach ($licenceData as $ocId => $row) {

            if (!isset($applicationData[$ocId])) {
                // If we have no application oc record

                // E for existing (No updates)
                $row['action'] = 'E';
                $data[] = $row;
            } elseif ($applicationData[$ocId]['action'] === 'U') {
                // If we have updated the operating centre

                $row['action'] = 'C';
                $data[] = $row;
            }
        }

        $data = array_merge($data, $applicationData);

        return $data;
    }

    protected function getIndexedTableData($type, $id)
    {
        $data = $this->getServiceLocator()->get('Entity\\' . $type . 'OperatingCentre')
            ->getAddressSummaryData($id)['Results'];

        $indexedData = [];

        foreach ($data as $value) {
            $value['id'] = substr($type, 0, 1) . $value['id'];
            $indexedData[$value['operatingCentre']['id']] = $value;
        }

        return $indexedData;
    }

    /**
     * Generic delete functionality; usually does the trick but
     * can be overridden if not
     */
    public function deleteAction()
    {
        if ($this->canDeleteRecord($this->params('child_id'))) {
            return parent::deleteAction();
        }

        // JS should restrict requests to only valid ones, however we better double check
        return $this->processUndeletableResponse();
    }

    public function restoreAction()
    {
        $ref = $this->params('child_id');

        list($type, $id) = $this->splitTypeAndId($ref);

        if ($type === 'A') {
            $this->getServiceLocator()->get('Entity\ApplicationOperatingCentre')
                ->delete($id);

            return $this->redirect()->toRouteAjax(null, array('action' => null, 'child_id' => null), array(), true);
        }

        // @todo restore updated version
    }

    protected function processUndeletableResponse()
    {
        $this->getServiceLocator()->get('Helper\FlashMessenger')
                ->addErrorMessage('could-not-remove-message');

        return $this->redirect()->toRouteAjax(null, array('child_id' => null), array(), true);
    }

    /**
     * This is more complicated than I would like, but we could have either an application oc record "A123" or a licence
     * operating centre record "L123", so we need to cater for both
     *
     * @return mixed
     */
    protected function delete()
    {
        $ref = $this->params('child_id');

        // JS should restrict requests to only valid ones, however we better double check
        if (!$this->canDeleteRecord($ref)) {
            return $this->processUndeletableResponse();
        }

        list($type, $id) = $this->splitTypeAndId($ref);

        if ($type === 'A') {
            $this->getServiceLocator()->get('Entity\ApplicationOperatingCentre')->delete($id);
            return;
        } else {
            $this->getServiceLocator()->get('Entity\LicenceOperatingCentre')
                ->variationDelete($id, $this->getApplicationId());
        }
    }

    protected function splitTypeAndId($ref)
    {
        $type = substr($ref, 0, 1);
        $id = (int)substr($ref, 1);

        return array($type, $id);
    }

    /**
     * Un-edited licence operating centre records and updated version stored in application operating centre
     * can be deleted
     *
     * @param type $ref
     */
    protected function canDeleteRecord($ref)
    {
        list($type, $id) = $this->splitTypeAndId($ref);

        $aocDataService = $this->getServiceLocator()->get('Entity\ApplicationOperatingCentre');

        // If we have an application operating centre record
        if ($type === 'A') {
            $record = $aocDataService->getById($id);

            return in_array($record['action'], ['U', 'A']);
        }

        $locDataService = $this->getServiceLocator()->get('Entity\LicenceOperatingCentre');

        $record = $locDataService->getAddressData($id);

        $ocId = $record['operatingCentre']['id'];

        $aocRecord = $aocDataService->getByApplicationAndOperatingCentre($this->getApplicationId(), $ocId);

        return empty($aocRecord);
    }
}

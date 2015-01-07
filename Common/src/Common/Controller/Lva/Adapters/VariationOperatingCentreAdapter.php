<?php

/**
 * Variation Operating Centre Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Zend\Form\Form;

/**
 * Variation Operating Centre Adapter
 *
 * @NOTE This could potentially extends the ApplicationOperatingCentreAdapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationOperatingCentreAdapter extends AbstractOperatingCentreAdapter
{
    const ACTION_ADDED = 'A';
    const ACTION_EXISTING = 'E';
    const ACTION_CURRENT = 'C';
    const ACTION_UPDATED = 'U';
    const ACTION_DELETED = 'D';

    const SOURCE_APPLICATION = 'A';
    const SOURCE_LICENCE = 'L';

    protected $lva = 'variation';

    protected $entityService = 'Entity\ApplicationOperatingCentre';

    protected $mainTableConfigName = 'lva-variation-operating-centres';

    /**
     * Attach the relevant scripts to the main page
     */
    public function attachMainScripts()
    {
        $this->getServiceLocator()->get('Script')->loadFile('lva-variation-operating-centre');
    }

    /**
     * Get address data
     *
     * @param int $id
     * @return array
     */
    public function getAddressData($id)
    {
        list($type, $ocId) = $this->splitTypeAndId($id);

        if ($type === self::SOURCE_APPLICATION) {
            return $this->getEntityService()->getAddressData($ocId);
        }

        return $this->getServiceLocator()->get('Entity\LicenceOperatingCentre')->getAddressData($ocId);
    }

    /**
     * Extend the abstract behaviour to get the table data for the main form
     *
     * @return array
     */
    public function getTableData()
    {
        if (empty($this->tableData)) {

            $licenceData = $this->getIndexedTableData('Licence', $this->getLicenceAdapter()->getIdentifier());
            $applicationData = $this->getIndexedTableData('Application', $this->getVariationAdapter()->getIdentifier());

            $data = $this->updateAndFilterTableData($licenceData, $applicationData);

            $this->tableData = $this->formatTableData($data);
        }

        return $this->tableData;
    }

    /**
     * Un-edited licence operating centre records and updated version stored in application operating centre
     * can be deleted
     *
     * @param type $ref
     */
    public function canDeleteRecord($ref)
    {
        list($type, $id) = $this->splitTypeAndId($ref);

        $aocDataService = $this->getEntityService();

        // If we have an application operating centre record
        if ($type === self::SOURCE_APPLICATION) {
            $record = $aocDataService->getById($id);

            return in_array($record['action'], [self::ACTION_UPDATED, self::ACTION_ADDED]);
        }

        $locDataService = $this->getServiceLocator()->get('Entity\LicenceOperatingCentre');

        $record = $locDataService->getAddressData($id);

        $ocId = $record['operatingCentre']['id'];

        $aocRecord = $aocDataService->getByApplicationAndOperatingCentre(
            $this->getVariationAdapter()->getIdentifier(),
            $ocId
        );

        return empty($aocRecord);
    }

    /**
     * Shared logic to create a response when a resource isn't deletable
     *
     * @return Zend\Http\Response
     */
    public function processUndeletableResponse()
    {
        $this->getServiceLocator()->get('Helper\FlashMessenger')
                ->addErrorMessage('could-not-remove-message');

        return $this->getController()->redirect()->toRouteAjax(null, array('child_id' => null), array(), true);
    }

    /**
     * Extend the abstract method so that it splits the id from the reference
     *
     * @return int
     */
    public function getChildId()
    {
        $ref = parent::getChildId();

        return $this->splitTypeAndId($ref)[1];
    }

    /**
     * This is more complicated than I would like, but we could have either an application oc record "A123" or a licence
     * operating centre record "L123", so we need to cater for both
     *
     * @return mixed
     */
    public function delete()
    {
        $ref = $this->getController()->params('child_id');

        // JS should restrict requests to only valid ones, however we better double check
        if (!$this->canDeleteRecord($ref)) {
            return $this->processUndeletableResponse();
        }

        list($type, $id) = $this->splitTypeAndId($ref);

        if ($type === self::SOURCE_APPLICATION) {
            $this->getEntityService()->delete($id);
            return;
        } else {
            $this->getServiceLocator()->get('Entity\LicenceOperatingCentre')
                ->variationDelete($id, $this->getIdentifier());
        }
    }

    public function restore()
    {
        $ref = $this->getController()->params('child_id');

        list($type, $id) = $this->splitTypeAndId($ref);

        // If we have an 'A'pplication_operating_centre record, deleting it should restore all scenarios
        if ($type === self::SOURCE_APPLICATION) {
            $this->getServiceLocator()->get('Entity\ApplicationOperatingCentre')
                ->delete($id);

            return $this->getController()->redirect()
                ->toRouteAjax(null, array('action' => null, 'child_id' => null), array(), true);
        }

        // @todo restore updated version
    }

    /**
     * Format the data for the form
     *
     * @param array $oldData
     * @param string $mode
     * @return array
     */
    public function formatCrudDataForForm(array $oldData, $mode)
    {
        $data = parent::formatCrudDataForForm($oldData, $mode);

        $action = $this->getOperatingCentreAction();

        if ($action === 'E') {
            unset($data['advertisements']);
        }

        return $data;
    }

    /**
     * Update and filter the table data for variations
     *
     * @param array $licenceData
     * @param array $applicationData
     * @return array
     */
    protected function updateAndFilterTableData($licenceData, $applicationData)
    {
        $data = array();

        foreach ($licenceData as $ocId => $row) {

            if (!isset($applicationData[$ocId])) {
                // If we have no application oc record

                // E for existing (No updates)
                $row['action'] = self::ACTION_EXISTING;
                $data[] = $row;
            } elseif ($applicationData[$ocId]['action'] === self::ACTION_UPDATED) {
                // If we have updated the operating centre

                $row['action'] = self::ACTION_CURRENT;
                $data[] = $row;
            }
        }

        $data = array_merge($data, $applicationData);

        return $data;
    }

    /**
     * Get the table data and index it for sorting
     *
     * @param string $type
     * @param int $id
     * @return array
     */
    protected function getIndexedTableData($type, $id)
    {
        $data = $this->getServiceLocator()->get('Entity\\' . $type . 'OperatingCentre')
            ->getAddressSummaryData($id)['Results'];

        $indexedData = [];

        foreach ($data as $value) {
            $value['source'] = substr($type, 0, 1);
            $value['id'] = $value['source'] . $value['id'];
            $indexedData[$value['operatingCentre']['id']] = $value;
        }

        return $indexedData;
    }

    /**
     * Splits a reference into type and if i.e. L123 returns ['L', 123]
     *
     * @param string $ref
     * @return array
     */
    protected function splitTypeAndId($ref)
    {
        $type = substr($ref, 0, 1);

        if (is_numeric($type)) {
            return array(null, $ref);
        }

        $id = (int)substr($ref, 1);

        return array($type, $id);
    }

    protected function getOperatingCentreAction($ref = null)
    {
        if ($ref == null) {
            $ref = $this->getController()->params('child_id');
        }

        $data = $this->getTableData();

        foreach ($data as $row) {
            if ($row['id'] === $ref) {
                return $row['action'];
            }
        }

        throw new \Exception('Operating centre not found');
    }

    /**
     * Alter action form
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function alterActionForm(Form $form)
    {
        $form = parent::alterActionForm($form);

        $action = $this->getOperatingCentreAction();

        if ($action !== 'A') {
            list($currentVehicles, $currentTrailers) = $this->getCurrentAuthorisationValues();

            $form->get('data')->get('noOfVehiclesRequired')->setAttribute('data-current', $currentVehicles);
            $form->get('data')->get('noOfTrailersRequired')->setAttribute('data-current', $currentTrailers);
        }

        return $form;
    }

    protected function getCurrentAuthorisationValues()
    {
        $ref = $this->getController()->params('child_id');
        list($type, $id) = $this->splitTypeAndId($ref);

        $data = $this->getTableData();

        if (!isset($data[$ref])) {
            throw new \Exception('Operating centre not found');
        }

        if ($type === self::SOURCE_LICENCE) {
            return [
                $data[$ref]['noOfVehiclesRequired'],
                $data[$ref]['noOfTrailersRequired']
            ];
        }

        $ocId = $data[$ref]['operatingCentre']['id'];

        foreach ($data as $row) {
            if ($row['source'] === self::SOURCE_LICENCE && $row['operatingCentre']['id'] == $ocId) {
                return [
                    $row['noOfVehiclesRequired'],
                    $row['noOfTrailersRequired']
                ];
            }
        }

        throw new \Exception('Operating centre not found');
    }
}

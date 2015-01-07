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
     * Extend the abstract behaviour to alter the action form
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    public function alterActionForm(Form $form)
    {
        $form = parent::alterActionForm($form);

        if ($this->location === 'external') {
            $formHelper = $this->getServiceLocator()->get('Helper\Form');
            $formHelper->disableElements($form->get('address'));
        }

        return $form;
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
        if ($type === 'A') {
            $record = $aocDataService->getById($id);

            return in_array($record['action'], ['U', 'A']);
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

        if ($type === 'A') {
            $this->getEntityService()->delete($id);
            return;
        } else {
            $this->getServiceLocator()->get('Entity\LicenceOperatingCentre')
                ->variationDelete($id, $this->getIdentifier());
        }
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
            $value['id'] = substr($type, 0, 1) . $value['id'];
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
}

<?php

/**
 * Abstract Operating Centre Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Zend\Form\Form;
use Zend\Http\Request;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Entity\TrafficAreaEntityService;
use Common\Service\Data\CategoryDataService;
use Common\Controller\Lva\Adapters\AbstractControllerAwareAdapter;
use Common\Controller\Lva\Interfaces\OperatingCentreAdapterInterface;
use Common\Service\Helper\FormHelperService;
use Common\Form\Elements\Validators\OcTotVehicleAuthPsvRestrictedValidator;

/**
 * Abstract Operating Centre Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractOperatingCentreAdapter extends AbstractControllerAwareAdapter implements
    OperatingCentreAdapterInterface
{
    const ACTION_ADDED = 'A';
    const ACTION_EXISTING = 'E';
    const ACTION_CURRENT = 'C';
    const ACTION_UPDATED = 'U';
    const ACTION_DELETED = 'D';
    const SOURCE_APPLICATION = 'A';
    const SOURCE_LICENCE = 'L';

    protected $tableData;

    protected $entityService;

    protected $mainTableConfigName = 'lva-operating-centres';

    /**
     * Data map
     *
     * @var array
     */
    protected $dataMap = array(
        'main' => array(
            'mapFrom' => array(
                'data',
                'dataTrafficArea'
            )
        )
    );

    /**
     * Action data map
     *
     * @var array
     */
    protected $actionDataMap = array(
        '_addresses' => array(
            'address'
        ),
        'main' => array(
            'children' => array(
                'applicationOperatingCentre' => array(
                    'mapFrom' => array(
                        'data',
                        'advertisements'
                    )
                ),
                'operatingCentre' => array(
                    'mapFrom' => array(
                        'operatingCentre'
                    ),
                    'children' => array(
                        'addresses' => array(
                            'mapFrom' => array(
                                'addresses'
                            )
                        )
                    )
                )
            )
        )
    );

    public function alterFormData($id, $data)
    {
        return $data;
    }

    public function alterFormDataOnPost($mode, $data, $childId)
    {
        return $data;
    }

    /**
     * Add messages to the main index page
     */
    public function addMessages($id)
    {
        // No-op by default
    }

    /**
     * Attach the relevant scripts to the main page
     */
    public function attachMainScripts()
    {
        $this->getServiceLocator()->get('Script')->loadFile('lva-crud');
    }

    /**
     * Get extra document properties to save
     *
     * @return array
     */
    public function getDocumentProperties()
    {
        return array(
            'application' => $this->getApplicationAdapter()->getIdentifier(),
            'licence' => $this->getLicenceAdapter()->getIdentifier()
        );
    }

    /**
     * Get operating centre data
     *
     * @param int $id
     * @return array
     */
    public function getOperatingCentresFormData($id)
    {
        return $this->formatDataForForm(
            $this->getLvaEntityService()->getOperatingCentresData($id),
            $this->getTableData(),
            $this->getTypeOfLicenceData()
        );
    }

    /**
     * Get the table data for the main form
     *
     * @return array
     */
    public function getTableData()
    {
        if (empty($this->tableData)) {

            $data = $this->getEntityService()->getAddressSummaryData($this->getIdentifier());

            $this->tableData = $this->formatTableData($data['Results']);
        }

        return $this->tableData;
    }

    /**
     * Create a prepared form for the given LVA type
     *
     * @return Zend\Form\Form
     */
    public function getMainForm()
    {
        $form = $this->createMainForm();

        $table = $this->createMainTable();

        $form->get('table')->get('table')->setTable($table);
        $form->get('table')->get('rows')->setValue(count($table->getRows()));

        $this->alterForm($form);

        return $form;
    }

    /**
     * Get an add/edit form based on the mode
     *
     * @param string $mode
     * @param \Zend\Http\Request $request
     * @return type
     */
    public function getActionForm($mode, Request $request)
    {
        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('Lva\OperatingCentre', $request);

        if ($mode !== 'add') {
            $form->get('form-actions')->remove('addAnother');
        }

        return $this->alterActionForm($form);
    }

    /**
     * By default our CRUD validation is to disable everything.
     * However, some LVAs want to extend this behaviour.
     *
     * @param \Zend\Form\Form $form
     */
    public function disableValidation(Form $form)
    {
        $this->getServiceLocator()->get('Helper\Form')->disableValidation($form->getInputFilter());
    }

    /**
     * Callback to populate files
     */
    public function getDocuments()
    {
        if (($id = $this->getChildId()) !== null) {
            $data = $this->getEntityService()->getAddressData($id);
            $operatingCentreId = $data['operatingCentre']['id'];
        } else {
            $operatingCentreId = null;
        }

        $documents = $this->getLvaEntityService()
            ->getDocuments(
                $this->getIdentifier(),
                CategoryDataService::CATEGORY_APPLICATION,
                CategoryDataService::DOC_SUB_CATEGORY_APPLICATION_ADVERT_DIGITAL
            );

        return array_filter(
            $documents,
            function ($doc) use ($operatingCentreId) {

                // always include 'unlinked' OCs
                if (empty($doc['operatingCentre'])) {
                    return true;
                }

                return $doc['operatingCentre']['id'] === $operatingCentreId;
            }
        );
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
        $data['data'] = $oldData;

        if ($mode !== 'add') {
            $data['operatingCentre'] = $data['data']['operatingCentre'];
            $data['address'] = $data['operatingCentre']['address'];
            $data['address']['countryCode'] = $data['address']['countryCode']['id'];

            $data['advertisements'] = array(
                'adPlaced' => $data['data']['adPlaced'],
                'adPlacedIn' => $data['data']['adPlacedIn'],
                'adPlacedDate' => $data['data']['adPlacedDate']
            );

            unset($data['data']['adPlaced']);
            unset($data['data']['adPlacedIn']);
            unset($data['data']['adPlacedDate']);
            unset($data['data']['operatingCentre']);
        }

        $trafficArea = $this->getTrafficArea();

        if (is_array($trafficArea) && isset($trafficArea['id'])) {
            $data['trafficArea'] = $trafficArea['id'];
        }

        return $data;
    }

    /**
     * Get the child id of the action
     *
     * @return int
     */
    public function getChildId()
    {
        return $this->getController()->params('child_id');
    }

    /**
     * Save the main form data
     *
     * @param array $data
     */
    public function saveMainFormData(array $data)
    {
        $appData = $this->formatDataForSave($data);

        if (isset($appData['trafficArea']) && $appData['trafficArea']) {

            $this->getServiceLocator()->get('Entity\Licence')
                ->setTrafficArea(
                    $this->getLicenceAdapter()->getIdentifier(),
                    $appData['trafficArea']
                );
        }

        $this->getLvaEntityService()->save($appData);
    }

    /**
     * Get address data
     *
     * @param int $id
     * @return array
     */
    public function getAddressData($id)
    {
        return $this->getEntityService()->getAddressData($id);
    }

    /**
     * Save action form data
     *
     * @param string $mode
     * @param array $data
     * @param array $formData
     * @throws \Exception
     */
    public function saveActionFormData($mode, array $data, array $formData)
    {
        $fileListData = array();

        if (isset($data['advertisements']['file']['list'])) {
            $fileListData = $data['advertisements']['file']['list'];
        }

        $data = $this->formatCrudDataForSave($formData);

        $saved = $this->getServiceLocator()->get('Entity\OperatingCentre')->save($data['operatingCentre']);

        if ($mode === 'add') {

            if (!isset($saved['id'])) {
                throw new \Exception('Unable to save operating centre');
            }

            $data['applicationOperatingCentre']['operatingCentre'] = $saved['id'];

            if (!isset($data['applicationOperatingCentre']['action'])) {
                $data['applicationOperatingCentre']['action'] = self::ACTION_ADDED;
            }

            $operatingCentreId = $saved['id'];
        } else {
            $operatingCentreId = $data['operatingCentre']['id'];
        }

        if (!empty($fileListData)) {
            $this->saveDocuments($fileListData, $operatingCentreId);
        }

        if ($this->isPsv()) {
            $data['applicationOperatingCentre']['adPlaced'] = 0;
        }

        $saved = $this->getEntityService()->save($data['applicationOperatingCentre']);

        if ($mode === 'add' && !isset($saved['id'])) {
            throw new \Exception('Unable to save operating centre');
        }

        $this->setDefaultTrafficAreaAfterActionSave($data);
    }

    /**
     * Set traffic area after action save
     */
    protected function setDefaultTrafficAreaAfterActionSave($data)
    {
        if (!isset($data['trafficArea']) || empty($data['trafficArea']['id'])) {
            $this->setDefaultTrafficArea($data);
        }
    }

    /**
     * Delete the selected children
     */
    public function delete()
    {
        $service = $this->getEntityService();

        $ids = explode(',', $this->getController()->params('child_id'));

        foreach ($ids as $id) {
            $service->delete($id);
        }
    }

    /**
     * Process address lookup for main form
     *
     * @param Form $form
     * @param Request $request
     * @return type
     */
    public function processAddressLookupForm($form, $request)
    {
        return $this->getServiceLocator()->get('Helper\Form')->processAddressLookupForm($form, $request);
    }

    /**
     * Format the table data for the main table
     *
     * @param array $results
     * @return array
     */
    protected function formatTableData($results)
    {
        $newData = array();

        foreach ($results as $row) {

            $newRow = $row;

            if (isset($row['operatingCentre']['address'])) {

                unset($row['operatingCentre']['address']['id']);
                unset($row['operatingCentre']['address']['version']);

                $newRow = array_merge($newRow, $row['operatingCentre']['address']);
            }

            $newData[$newRow['id']] = $newRow;
        }

        return $newData;
    }

    /**
     * Get the relevant ****OperatingCentre entity service based on the LVA type
     *
     * @return Common\Service\Entity\AbstractEntityService
     */
    protected function getEntityService()
    {
        return $this->getServiceLocator()->get($this->entityService);
    }

    /**
     * Create the main form using the form helper
     *
     * @return Zend\Form\Form
     */
    protected function createMainForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\OperatingCentres');
    }

    /**
     * Create the main table for the main form, using the table builder
     *
     * @return Common\Service\Table\TableBuilder
     */
    protected function createMainTable()
    {
        return $this->getServiceLocator()
            ->get('Table')
            ->prepareTable($this->getMainTableConfigName(), $this->getTableData());
    }

    /**
     * Get table config name (This is different depending on LVA type)
     *
     * @return string
     */
    protected function getMainTableConfigName()
    {
        return $this->mainTableConfigName;
    }

    /**
     * Alter the main form
     *
     * @param Zend\Form\Form $form
     * @return Zend\Form\Form
     */
    public function alterForm(Form $form)
    {
        $this->getLvaAdapter()->alterForm($form);

        if ($this->isPsv()) {
            $this->alterFormForPsvLicences($form);
            $this->alterFormTableForPsv($form);
        } else {
            $this->alterFormForGoodsLicences($form);
        }

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        // modify the table validation message
        $formHelper->getValidator($form, 'table->table', 'Common\Form\Elements\Validators\TableRequiredValidator')
            ->setMessage('OperatingCentreNoOfOperatingCentres.required', 'required');

        $tableData = $this->getTableData();

        if (empty($tableData)) {
            $formHelper->remove($form, 'dataTrafficArea');
            return $form;
        }

        $trafficArea = $this->getTrafficArea();
        $trafficAreaId = $trafficArea ? $trafficArea['id'] : '';

        $dataTrafficAreaFieldset = $form->get('dataTrafficArea');

        if ($trafficAreaId) {

            $formHelper->remove($form, 'dataTrafficArea->trafficArea');
            $dataTrafficAreaFieldset->get('trafficAreaSet')
                ->setValue($trafficArea['name'])
                ->setOption('hint-suffix', '-operating-centres');

            return $form;
        }

        $options = $this->getServiceLocator()->get('Entity\TrafficArea')->getValueOptions();

        $dataTrafficAreaFieldset->remove('trafficAreaSet')
            ->get('trafficArea')
            ->setValueOptions($options);

        return $form;
    }

    /**
     * Get the current records, type of licence data
     *
     * @return array
     */
    protected function getTypeOfLicenceData()
    {
        return $this->getLvaEntityService()->getTypeOfLicenceData($this->getIdentifier());
    }

    /**
     * If the current record is a psv
     *
     * @return boolean
     */
    protected function isPsv()
    {
        $data = $this->getTypeOfLicenceData();
        return isset($data['goodsOrPsv']) && $data['goodsOrPsv'] === LicenceEntityService::LICENCE_CATEGORY_PSV;
    }

    /**
     * Alter form for PSV applications
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterFormForPsvLicences(Form $form)
    {
        $dataOptions = $form->get('data')->getOptions();
        $dataOptions['hint'] .= '.psv';
        $form->get('data')->setOptions($dataOptions);

        $removeFields = array(
            'totAuthTrailers',
            'minTrailerAuth',
            'maxTrailerAuth'
        );

        $licenceData = $this->getTypeOfLicenceData();

        $licenceType = $licenceData['licenceType'];

        $allowLargeVehicles = array(
            LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
            LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
        );

        $allowCommunityLicences = array(
            LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
            LicenceEntityService::LICENCE_TYPE_RESTRICTED
        );

        if (!in_array($licenceType, $allowLargeVehicles)) {
            $removeFields[] = 'totAuthLargeVehicles';
        }

        if (!in_array($licenceType, $allowCommunityLicences)) {
            $removeFields[] = 'totCommunityLicences';
        }

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $formHelper->removeFieldList($form, 'data', $removeFields);

        if ($licenceData['goodsOrPsv'] == LicenceEntityService::LICENCE_CATEGORY_PSV
            && $licenceType == LicenceEntityService::LICENCE_TYPE_RESTRICTED) {

            $validator = new OcTotVehicleAuthPsvRestrictedValidator();

            $formHelper->attachValidator($form, 'data->totAuthVehicles', $validator);
        }
    }

    /**
     * Alter form table for PSV
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterFormTableForPsv(Form $form)
    {
        $table = $form->get('table')->get('table')->getTable();

        $table->removeColumn('noOfTrailersRequired');

        $footer = $table->getFooter();
        if (isset($footer['total']['content'])) {
            $footer['total']['content'] .= '-psv';
            unset($footer['trailersCol']);
            $table->setFooter($footer);
        }
    }

    /**
     * Alter form for GOODS applications
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterFormForGoodsLicences(Form $form)
    {
        $removeFields = array(
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles'
        );

        $typeOfLicence = $this->getTypeOfLicenceData();
        if ($typeOfLicence['licenceType'] !== LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL) {
            $removeFields[] = 'totCommunityLicences';
        }

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $formHelper->removeFieldList($form, 'data', $removeFields);

        $formHelper->removeValidator($form, 'data->totAuthVehicles', 'Common\Form\Elements\Validators\EqualSum');
    }

    /**
     * Get the traffic area for the resource
     *
     * @param int $lvaId
     * @return string
     */
    protected function getTrafficArea($lvaId = null)
    {
        if ($lvaId === null) {
            $lvaId = $this->getLicenceAdapter()->getIdentifier();
        }

        return $this->getServiceLocator()->get('Entity\Licence')->getTrafficArea($lvaId);
    }

    /**
     * Format data for form
     *
     * @param array $data
     * @param array $tableData
     * @param array $licenceData
     * @return array
     */
    protected function formatDataForForm(array $data, array $tableData, array $licenceData)
    {
        $data['data'] = $oldData = $data;

        $data['data']['noOfOperatingCentres'] = count($tableData);
        $data['data']['minVehicleAuth'] = 0;
        $data['data']['maxVehicleAuth'] = 0;
        $data['data']['minTrailerAuth'] = 0;
        $data['data']['maxTrailerAuth'] = 0;
        $data['data']['licenceType'] = $licenceData['licenceType'];

        foreach ($tableData as $row) {

            $data['data']['minVehicleAuth'] = max(
                array($data['data']['minVehicleAuth'], $row['noOfVehiclesRequired'])
            );

            $data['data']['minTrailerAuth'] = max(
                array($data['data']['minTrailerAuth'], $row['noOfTrailersRequired'])
            );

            $data['data']['maxVehicleAuth'] += (int)$row['noOfVehiclesRequired'];
            $data['data']['maxTrailerAuth'] += (int)$row['noOfTrailersRequired'];
        }

        if (isset($oldData['licence']['trafficArea']['id'])) {
            $data['dataTrafficArea']['hiddenId'] = $oldData['licence']['trafficArea']['id'];
        }

        return $data;
    }

    /**
     * Alter action form
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    public function alterActionForm(Form $form)
    {
        if ($this->isPsv()) {
            $this->alterActionFormForPsv($form);
        } else {
            $this->alterActionFormForGoods($form);
        }

        $this->alterFormForTrafficArea($form);

        return $form;
    }

    /**
     * Alter action form for PSV licences
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterActionFormForPsv(Form $form)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $formHelper->remove($form, 'data->noOfTrailersRequired');
        $formHelper->remove($form, 'advertisements');

        $formHelper->alterElementLabel(
            $form->get('data'),
            '-psv',
            FormHelperService::ALTER_LABEL_APPEND
        );
        $formHelper->alterElementLabel(
            $form->get('data')->get('sufficientParking'),
            '-psv',
            FormHelperService::ALTER_LABEL_APPEND
        );
        $formHelper->alterElementLabel(
            $form->get('data')->get('permission'),
            '-psv',
            FormHelperService::ALTER_LABEL_APPEND
        );
    }

    /**
     * Alter action form for Goods licences
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterActionFormForGoods(Form $form)
    {
        // No-op by default
    }

    /**
     * Alter the form with all the traffic area stuff
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterFormForTrafficArea(Form $form)
    {
        $licenceData = $this->getTypeOfLicenceData();
        $trafficArea = $this->getTrafficArea();

        $trafficAreaValidator = $this->getServiceLocator()->get('postcodeTrafficAreaValidator');
        $trafficAreaValidator->setNiFlag($licenceData['niFlag']);
        $trafficAreaValidator->setOperatingCentresCount($this->getOperatingCentresCount());
        $trafficAreaValidator->setTrafficArea($trafficArea);

        // Set the postcode field as not required and attach a new validator
        $form->getInputFilter()->get('address')->get('postcode')
            ->setRequired(false)
            ->getValidatorChain()->attach($trafficAreaValidator);

        if ($licenceData['niFlag'] == 'N' && !$trafficArea && $form->get('form-actions')->has('addAnother')) {
            $form->get('form-actions')->remove('addAnother');
        }
    }

    /**
     * Count the operating services
     *
     * @return int
     */
    protected function getOperatingCentresCount()
    {
        $operatingCentres = $this->getEntityService()
            ->getOperatingCentresCount($this->getIdentifier());

        return $operatingCentres['Count'];
    }

    /**
     * Format the action data for saving
     *
     * @param array $crudData
     * @return array
     */
    protected function formatCrudDataForSave($crudData)
    {
        $data = $this->getServiceLocator()
            ->get('Helper\Data')
            ->processDataMap($crudData, $this->actionDataMap);

        $type = $this->lva === 'variation' ? 'application' : $this->lva;

        // we no longer store this in the form...
        $data['applicationOperatingCentre'][$type] = $this->getIdentifier();

        return $data;
    }

    /**
     * Set the default traffic area
     *
     * @param array $data
     */
    protected function setDefaultTrafficArea($data)
    {
        $licenceData = $this->getTypeOfLicenceData();

        if ($licenceData['niFlag'] === 'Y') {
            $this->getServiceLocator()
                ->get('Entity\Licence')
                ->setTrafficArea(
                    $this->getLicenceAdapter()->getIdentifier(),
                    TrafficAreaEntityService::NORTHERN_IRELAND_TRAFFIC_AREA_CODE
                );
            return;
        }

        if (isset($data['operatingCentre']['addresses']['address'])) {
            $postcode = $data['operatingCentre']['addresses']['address']['postcode'];
        }

        if (!empty($postcode) && $this->getOperatingCentresCount() === 1) {

            $postcodeService = $this->getServiceLocator()->get('Postcode');

            $trafficAreaParts = $postcodeService->getTrafficAreaByPostcode($postcode);

            if (!empty($trafficAreaParts)) {
                $this->getServiceLocator()
                    ->get('Entity\Licence')
                    ->setTrafficArea(
                        $this->getLicenceAdapter()->getIdentifier(),
                        array_shift($trafficAreaParts)
                    );
            }
        }
    }

    /**
     * Save the (previously unlinked) documents
     *
     * @param array $data
     * @param int $operatingCentreId
     */
    protected function saveDocuments(array $data, $operatingCentreId)
    {
        if (!empty($data)) {
            $documentService = $this->getServiceLocator()->get('Entity\Document');
        }

        foreach ($data as $file) {

            $documentData = array(
                'id' => $file['id'],
                'version' => $file['version'],
                'operatingCentre' => $operatingCentreId
            );

            $documentService->save($documentData);
        }
    }

    /**
     * Format data for save
     *
     * @param array $data
     * @return array
     */
    protected function formatDataForSave(array $data)
    {
        return $this->getServiceLocator()
            ->get('Helper\Data')
            ->processDataMap($data, $this->dataMap);
    }

    /**
     * Grab the main resource id from the route, based on LVA
     *
     * @return int
     */
    protected function getIdentifier()
    {
        return $this->getLvaAdapter()->getIdentifier();
    }

    /**
     * Disable and lock address fields
     *
     * @param \Zend\Form\Form $form
     */
    protected function disableAddressFields($form)
    {
        $addressElement = $form->get('address');
        $addressElement->remove('searchPostcode');

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $formHelper->disableElements($addressElement);
        $formHelper->disableValidation($form->getInputFilter()->get('address'));

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $lockedElements = array(
            $addressElement->get('addressLine1'),
            $addressElement->get('town'),
            $addressElement->get('postcode'),
            $addressElement->get('countryCode'),
        );

        foreach ($lockedElements as $element) {
            $formHelper->lockElement($element, 'operating-centre-address-requires-variation');
        }
    }
}

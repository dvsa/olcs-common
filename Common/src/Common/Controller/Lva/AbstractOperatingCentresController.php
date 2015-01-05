<?php

/**
 * Shared logic between Operating Centres controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */

namespace Common\Controller\Lva;

use Zend\Form\Form;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Entity\TrafficAreaEntityService;
use Common\Service\Data\CategoryDataService;

/**
 * Shared logic between Operating Centres controllers
 *
 * @TODO This section needs a looking over, especially regarding traffic area stuff/postcode lookup
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractOperatingCentresController extends AbstractController
{
    use Traits\CrudTableTrait;

    protected $tableData = array();

    /**
     * Needed by the Crud Table Trait
     */
    private $section = 'operating_centres';

    /**
     * These vary depending on licence or application
     */
    abstract protected function getDocumentProperties();

    protected function getTrafficArea($lvaId = null)
    {
        if ($lvaId === null) {
            $lvaId = $this->getLicenceId();
        }
        return $this->getServiceLocator()
            ->get('Entity\Licence')
            ->getTrafficArea($lvaId);
    }

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

    /**
     * Get the entity name representing this LVA type. By default
     * we can work this out, but child controllers can override
     * if needs be
     */
    protected function getLvaEntity()
    {
        return 'Entity\\' . ucfirst($this->lva);
    }

    /**
     * Get the entity name representing this LVA's Operating Centres
     */
    protected function getLvaOperatingCentreEntity()
    {
        return 'Entity\\' . ucfirst($this->lva) . 'OperatingCentre';
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $lvaEntity = $this->getLvaEntity();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->getServiceLocator()->get($lvaEntity)
                ->getOperatingCentresData($this->getIdentifier());

            $data = $this->formatDataForForm($data);
        }

        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createForm('Lva\OperatingCentres');

        $table = $this->getServiceLocator()
            ->get('Table')
            ->prepareTable('authorisation_in_form', $this->getTableData());

        $form->get('table')
            ->get('table')
            ->setTable($table);

        $form = $this->alterForm($form)
            ->setData($data);

        if ($request->isPost()) {

            $crudAction = $this->getCrudAction(array($data['table']));

            if ($crudAction !== null) {
                $this->disableConditionalValidation($form);
            }

            if ($form->isValid()) {
                $appData = $this->formatDataForSave($data);

                if (isset($appData['trafficArea']) && $appData['trafficArea']) {
                    $this->getServiceLocator()->get('Entity\Licence')
                        ->setTrafficArea(
                            $this->getLicenceId(),
                            $appData['trafficArea']
                        );
                }

                $this->getServiceLocator()->get($lvaEntity)
                    ->save($appData);

                if ($crudAction !== null) {
                    return $this->handleCrudAction($crudAction);
                }

                $this->postSave('operating_centres');

                return $this->completeSection('operating_centres');
            }
        }

        $this->getServiceLocator()->get('Script')->loadFile('lva-crud');

        return $this->render('operating_centres', $form);
    }

    /**
     * Make form alterations
     *
     * This method enables the summary to apply the same form alterations. In this
     * case we ensure we manipulate the form based on whether the license is PSV or not
     *
     * @param \Zend\Form\Form $form
     * @param array $options
     *
     * @return $form
     */
    public function makeFormAlterations(Form $form, $options = array())
    {
        $fieldsetMap = $this->getFieldsetMap($form, $options);

        if ($options['isPsv']) {
            $this->alterFormForPsvLicences($form, $fieldsetMap, $options);
            $this->alterFormTableForPsv($form, $fieldsetMap);
        } else {
            $this->alterFormForGoodsLicences($form, $fieldsetMap);
        }

        return $form;
    }

    /**
     * Need to enumerate the form fieldsets with their mapping, as we're going to use old/new
     *
     * @param \Zend\Form\Form $form
     * @param array $options
     */
    protected function getFieldsetMap(Form $form, $options)
    {
        if (!$options['isReview']) {

            return array(
                'dataTrafficArea' => 'dataTrafficArea',
                'data' => 'data',
                'table' => 'table'
            );
        }

        $fieldsetMap = array();

        foreach ($options['fieldsets'] as $fieldset) {
            $fieldsetMap[$form->get($fieldset)->getAttribute('unmappedName')] = $fieldset;
        }

        return $fieldsetMap;
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
        $form->getInputFilter()->get('address')->get('postcode')->setRequired(false)
            ->getValidatorChain()->attach($trafficAreaValidator);

        $actions = $form->get('form-actions');

        if ($licenceData['niFlag'] == 'N' && !$trafficArea && $actions->has('addAnother')) {
            $actions->remove('addAnother');
        }
    }

    /**
     * Get the alter form options
     *
     * @return array
     */
    protected function getAlterFormOptions()
    {
        $licenceData = $this->getTypeOfLicenceData();

        return array(
            'isPsv' => $this->isPsv(),
            'isReview' => false,
            'data' => array(
                'licence' => array(
                    'licenceType' => array(
                        'id' => $licenceData['licenceType']
                    )
                )
            )
        );
    }

    /**
     * Alter form for GOODS applications
     *
     * @param \Zend\Form\Form $form
     * @param array $fieldsetMap
     */
    protected function alterFormForGoodsLicences(Form $form, $fieldsetMap)
    {
        $removeFields = array(
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles',
            'totCommunityLicences'
        );

        $this->getServiceLocator()->get('Helper\Form')->removeFieldList($form, $fieldsetMap['data'], $removeFields);
    }

    /**
     * Get the table data for the main form
     *
     * @return array
     */
    private function getTableData()
    {
        $lvaEntity = $this->getLvaOperatingCentreEntity();

        if (empty($this->tableData)) {
            $id = $this->getIdentifier();

            $data = $this->getServiceLocator()->get($lvaEntity)
                ->getAddressSummaryData($id);

            $newData = array();

            foreach ($data['Results'] as $row) {

                $newRow = $row;

                if (isset($row['operatingCentre']['address'])) {

                    unset($row['operatingCentre']['address']['id']);
                    unset($row['operatingCentre']['address']['version']);

                    $newRow = array_merge($newRow, $row['operatingCentre']['address']);
                }

                unset($newRow['operatingCentre']);

                $newData[] = $newRow;
            }
            $this->tableData = $newData;
        }

        return $this->tableData;
    }

    public function addAction()
    {
        return $this->addOrEdit('add');
    }

    public function editAction()
    {
        return $this->addOrEdit('edit');
    }

    private function addOrEdit($mode)
    {
        $lvaEntity = $this->getLvaOperatingCentreEntity();

        $this->getServiceLocator()->get('Script')->loadFile('add-operating-centre');

        $id = $this->params('child_id');
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            if ($mode === 'edit') {
                $data = $this->getServiceLocator()->get($lvaEntity)->getAddressData($id);
            } else {
                $data = [];
            }
            $data = $this->formatCrudDataForForm($data, $mode);
        }

        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('Lva\OperatingCentre', $request)
            ->setData($data);

        if ($mode !== 'add') {
            $form->get('form-actions')->remove('addAnother');
        }

        $form = $this->alterActionForm($form);

        $hasProcessedPostcode = $this->getServiceLocator()->get('Helper\Form')
            ->processAddressLookupForm($form, $request);

        if ($form->has('advertisements')) {
            $hasProcessedFiles = $this->processFiles(
                $form,
                'advertisements->file',
                array($this, 'processAdvertisementFileUpload'),
                array($this, 'deleteFile'),
                array($this, 'getDocuments')
            );
        } else {
            $hasProcessedFiles = false;
        }

        if (!$hasProcessedFiles && !$hasProcessedPostcode && $request->isPost() && $form->isValid()) {

            $fileListData = array();
            if (isset($data['advertisements']['file']['list'])) {
                $fileListData = $data['advertisements']['file']['list'];
            }

            $data = $this->formatCrudDataForSave($form->getData());

            $saved = $this->getServiceLocator()->get('Entity\OperatingCentre')->save($data['operatingCentre']);

            if ($mode === 'add') {

                if (!isset($saved['id'])) {
                    throw new \Exception('Unable to save operating centre');
                }

                $data['applicationOperatingCentre']['operatingCentre'] = $saved['id'];

                // @NOTE Mark the OC as being added, for when we are validating
                // This will only apply to application OCs
                // We will also need @todo something for variation when editing or deleting.
                $data['applicationOperatingCentre']['action'] = 'A';

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

            $saved = $this->getServiceLocator()->get($lvaEntity)->save($data['applicationOperatingCentre']);

            if ($mode === 'add' && !isset($saved['id'])) {
                throw new \Exception('Unable to save operating centre');
            }

            // set default Traffic Area if we don't have one
            if (!isset($data['trafficArea']) || empty($data['trafficArea']['id'])) {
                $this->setDefaultTrafficArea($data);
            }

            return $this->handlePostSave();
        }

        return $this->render($mode . '_operating_centre', $form);
    }

    protected function delete()
    {
        $lvaEntity = $this->getLvaOperatingCentreEntity();

        $service = $this->getServiceLocator()->get($lvaEntity);

        $ids = explode(',', $this->params('child_id'));

        foreach ($ids as $id) {
            $service->delete($id);
        }
    }

    protected function formatCrudDataForSave($data)
    {
        $data = $this->getServiceLocator()
            ->get('Helper\Data')
            ->processDataMap($data, $this->actionDataMap);

        // we no longer store this in the form...
        $data['applicationOperatingCentre'][$this->lva] = $this->getIdentifier();

        return $data;
    }

    protected function formatCrudDataForForm($oldData, $mode)
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
                    $this->getLicenceId(),
                    TrafficAreaEntityService::NORTHERN_IRELAND_TRAFFIC_AREA_CODE
                );
            return;
        }

        if (isset($data['operatingCentre']['addresses']['address'])) {
            $postcode = $data['operatingCentre']['addresses']['address']['postcode'];
        }

        if (!empty($postcode) && $this->getOperatingCentresCount() === 1) {

            $postcodeService = $this->getServiceLocator()
                ->get('Postcode');

            $trafficAreaParts = $postcodeService->getTrafficAreaByPostcode($postcode);

            if (!empty($trafficAreaParts)) {
                $this->getServiceLocator()
                    ->get('Entity\Licence')
                    ->setTrafficArea(
                        $this->getLicenceId(),
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
    protected function saveDocuments($data, $operatingCentreId)
    {
        foreach ($data as $file) {
            $this->getServiceLocator()->get('Helper\Rest')->makeRestCall(
                'Document',
                'PUT',
                array('id' => $file['id'], 'version' => $file['version'], 'operatingCentre' => $operatingCentreId)
            );
        }
    }

    private function formatDataForForm($data)
    {
        $data['data'] = $oldData = $data;

        $results = $this->getTableData();

        $licenceData = $this->getTypeOfLicenceData();

        $data['data']['noOfOperatingCentres'] = count($results);
        $data['data']['minVehicleAuth'] = 0;
        $data['data']['maxVehicleAuth'] = 0;
        $data['data']['minTrailerAuth'] = 0;
        $data['data']['maxTrailerAuth'] = 0;
        $data['data']['licenceType'] = $licenceData['licenceType'];

        foreach ($results as $row) {

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

    private function formatDataForSave($data)
    {
        return $this->getServiceLocator()
            ->get('Helper\Data')
            ->processDataMap($data, $this->dataMap);
    }

    public function getOperatingCentresCount()
    {
        $lvaEntity = $this->getLvaOperatingCentreEntity();
        $operatingCentres = $this->getServiceLocator()->get($lvaEntity)
            ->getOperatingCentresCount($this->getIdentifier());

        return $operatingCentres['Count'];
    }

    /**
     * Alter action form
     *
     * @param Form $form
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

        $dataLabel = $form->get('data')->getLabel();
        $form->get('data')->setLabel($dataLabel . '-psv');

        $parkingLabel = $form->get('data')->get('sufficientParking')->getLabel();
        $form->get('data')->get('sufficientParking')->setLabel($parkingLabel . '-psv');

        $permissionLabel = $form->get('data')->get('permission')->getLabel();
        $form->get('data')->get('permission')->setLabel($permissionLabel . '-psv');
    }

    /**
     * Alter action form for Goods licences
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterActionFormForGoods(Form $form)
    {
        // used to be abstract... could change it back?
    }

    protected function isPsv()
    {
        $data = $this->getTypeOfLicenceData();
        return isset($data['goodsOrPsv']) && $data['goodsOrPsv'] === LicenceEntityService::LICENCE_CATEGORY_PSV;
    }

    /**
     * Remove trailer elements for PSV and set up Traffic Area section
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function alterForm(Form $form)
    {
        $this->alterFormForLva($form);

        $tableData = $this->getTableData();

        // Make the same form alterations that are required for the summary section
        $form = $this->makeFormAlterations($form, $this->getAlterFormOptions());

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        if (empty($tableData)) {
            $formHelper->remove($form, 'dataTrafficArea');
            return $form;
        }

        $trafficArea = $this->getTrafficArea();
        $trafficAreaId = $trafficArea ? $trafficArea['id'] : '';

        $dataTrafficAreaFieldset = $form->get('dataTrafficArea');

        if ($trafficAreaId) {

            $formHelper->remove($form, 'dataTrafficArea->trafficArea');
            $nameExistsElement = $dataTrafficAreaFieldset->get('trafficAreaInfoNameExists');

            $nameExistsElement->setValue(
                str_replace('%NAME%', $trafficArea['name'], $nameExistsElement->getValue())
            );
            return $form;
        }
        $options = $this->getServiceLocator()
            ->get('Entity\TrafficArea')->getValueOptions();

        $dataTrafficAreaFieldset->remove('trafficAreaInfoLabelExists')
            ->remove('trafficAreaInfoNameExists')
            ->remove('trafficAreaInfoHintExists')
            ->get('trafficArea')
            ->setValueOptions($options);

        return $form;
    }

    /**
     * Alter form table for PSV
     *
     * @param \Zend\Form\Form $form
     * @param array $fieldsetMap
     */
    protected function alterFormTableForPsv(Form $form, $fieldsetMap)
    {
        $table = $form->get($fieldsetMap['table'])->get('table')->getTable();

        $table->removeColumn('noOfTrailersRequired');

        $footer = $table->getFooter();
        $footer['total']['content'] .= '-psv';
        unset($footer['trailersCol']);
        $table->setFooter($footer);
    }

    /**
     * Alter form hint for psv
     *
     * @param \Zend\Form\Form $form
     * @param array $fieldsetMap
     */
    protected function alterFormHintForPsv(Form $form, $fieldsetMap)
    {
        $formOptions = $form->get($fieldsetMap['data'])->getOptions();
        $formOptions['hint'] .= '.psv';
        $form->get($fieldsetMap['data'])->setOptions($formOptions);
    }

    /**
     * Alter form for PSV applications
     *
     * @param \Zend\Form\Form $form
     * @param array $fieldsetMap
     * @param array $options
     */
    protected function alterFormForPsvLicences(Form $form, $fieldsetMap, $options)
    {
        $this->alterFormHintForPsv($form, $fieldsetMap);

        $removeFields = array(
            'totAuthVehicles',
            'totAuthTrailers',
            'minTrailerAuth',
            'maxTrailerAuth'
        );

        $licenceType = $options['data']['licence']['licenceType']['id'];

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

        $this->getServiceLocator()->get('Helper\Form')->removeFieldList($form, $fieldsetMap['data'], $removeFields);
    }

    /**
     * Add variation info message
     */
    protected function addVariationInfoMessage()
    {
        $params = [
            // variations are *always* created from a licence. Sure, we only expect this message to appear
            // in the context of a licence, but let's be absolutely sure by hardcoding the key & get method
            // instead of using the abstract LVA ones
            'licence' => $this->getLicenceId()
        ];

        $this->addCurrentMessage(
            $this->getServiceLocator()->get('Helper\Translation')->formatTranslation(
                '%s <a href="' . $this->url()->fromRoute('create_variation', $params) . '">%s</a>',
                array(
                    'variation-application-text',
                    'variation-application-link-text'
                )
            ),
            'info'
        );
    }

    /**
     * Handle the file upload
     *
     * @param array $file
     */
    public function processAdvertisementFileUpload($file)
    {
        $categoryService = $this->getServiceLocator()->get('category');

        // The top-level category is *always* application; this is correct
        $category = $categoryService->getCategoryByDescription('Application');
        $subCategory = $categoryService->getCategoryByDescription('Advert Digital', 'Document');

        $this->uploadFile(
            $file,
            array_merge(
                array(
                    'description' => 'Advertisement',
                    'category'    => $category['id'],
                    'subCategory' => $subCategory['id'],
                ),
                $this->getDocumentProperties()
            )
        );
    }

    /**
     * Callback to populate files
     */
    public function getDocuments()
    {
        $lvaEntity = $this->getLvaEntity();
        $lvaOcEntity = $this->getLvaOperatingCentreEntity();

        if (($id = $this->params('child_id')) !== null) {
            $data = $this->getServiceLocator()->get($lvaOcEntity)->getAddressData($id);
            $operatingCentreId = $data['operatingCentre']['id'];
        } else {
            $operatingCentreId = null;
        }

        $documents = $this->getServiceLocator()->get($lvaEntity)
            ->getDocuments(
                $this->getIdentifier(),
                CategoryDataService::CATEGORY_APPLICATION,
                CategoryDataService::DOC_SUB_CATEGORY_APPLICATION_ADVERT_DIGITAL
            );

        return array_filter(
            $documents,
            function ($d) use ($operatingCentreId) {

                // always include 'unlinked' OCs
                if (empty($d['operatingCentre'])) {
                    return true;
                }

                return $d['operatingCentre']['id'] === $operatingCentreId;
            }
        );
    }

    /**
     * By default our conditional validation is the standard
     * mechanism to not validate empty fields. However, some LVAs
     * want to extend this behaviour
     *
     * @param \Zend\Form\Form $form
     */
    protected function disableConditionalValidation(Form $form)
    {
        $this->getServiceLocator()->get('Helper\Form')->disableEmptyValidation($form);
    }
}

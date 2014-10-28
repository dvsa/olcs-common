<?php

/**
 * Shared logic between Operating Centres controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */

namespace Common\Controller\Lva;

use Zend\Form\Form;
use Common\Service\Entity\LicenceEntityService;

/**
 * Shared logic between Operating Centres controllers
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

    protected function getIdentifier()
    {
        return $this->params('id');
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
     * Index action
     */
    public function indexAction()
    {
        $lvaEntity = 'Entity\\' . ucfirst($this->lva);
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

        $column = $table->getColumn('address');
        $column['type'] = $this->lva;
        $table->setColumn('address', $column);

        $form->get('table')
            ->get('table')
            ->setTable($table);

        $form = $this->alterForm($form)
            ->setData($data);

        if ($request->isPost() && $form->isValid()) {

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

            if (isset($data['table']['action'])) {
                return $this->handleCrudAction($data['table']);
            }
            return $this->completeSection('operating_centres');
        }

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

        if ($options['isReview']) {
            $form->get($fieldsetMap['dataTrafficArea'])->remove('trafficArea');

            $this->getTrafficArea($options['data']['id']);

            if (!isset($trafficArea['name'])) {
                $trafficArea['name'] = 'unset';
            }

            $form->get($fieldsetMap['dataTrafficArea'])->get('trafficAreaInfoNameExists')
                ->setValue($trafficArea['name']);
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

        if ($licenceData['niFlag'] == 'N' && !$trafficArea) {
            $form->get('form-actions')->remove('addAnother');
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

        $this->removeFormFields($form, $fieldsetMap['data'], $removeFields);
    }

    /**
     * Remove a list of form fields
     *
     * @TODO was in abstract section service... do we need it? Helper maybe?
     *
     * @param \Zend\Form\Form $form
     * @param string $fieldset
     * @param array $fields
     */
    public function removeFormFields(Form $form, $fieldset, array $fields)
    {
        foreach ($fields as $field) {
            $form->get($fieldset)->remove($field);
        }
    }

    /**
     * Get the table data for the main form
     *
     * @return array
     */
    private function getTableData()
    {
        $lvaEntity = 'Entity\\' . ucfirst($this->lva) . 'OperatingCentre';

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
        $lvaEntity = 'Entity\\' . ucfirst($this->lva) . 'OperatingCentre';

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
            ->createForm('Lva\OperatingCentre')
            ->setData($data);

        $form = $this->alterActionForm($form);

        $hasProcessedPostcode = $this->getServiceLocator()->get('Helper\Form')->processAddressLookupForm($form, $request);

        $hasProcessedFiles = $this->processFiles(
            $form,
            'advertisements->file',
            array($this, 'processAdvertisementFileUpload'),
            array($this, 'deleteFile'),
            array($this, 'getDocuments')
        );

        if (!$hasProcessedFiles && !$hasProcessedPostcode && $request->isPost() && $form->isValid()) {
            $data = $this->formatCrudDataForSave($data);

            $saved = $this->getServiceLocator()->get('Entity\OperatingCentre')->save($data['operatingCentre']);

            if ($mode === 'add') {

                if (!isset($saved['id'])) {
                    throw new \Exception('Unable to save operating centre');
                }

                $data['applicationOperatingCentre']['operatingCentre'] = $saved['id'];

                $operatingCentreId = $saved['id'];
            } else {
                $operatingCentreId = $data['operatingCentre']['id'];
            }

            $this->saveDocuments($data, $operatingCentreId);

            if ($this->isPsv()) {
                $data['applicationOperatingCentre']['adPlaced'] = 0;
            }

            // @todo not sure this is right
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
        $lvaEntity = 'Entity\\' . ucfirst($this->lva) . 'OperatingCentre';

        $this->getServiceLocator()
            ->get($lvaEntity)
            ->delete($this->params('child_id'));
    }

    protected function formatCrudDataForSave($data)
    {
        $data = $this->getServiceLocator()
            ->get('Helper\Data')
            ->processDataMap($data, $this->actionDataMap);

        if (isset($data['applicationOperatingCentre']['adPlacedDate'])) {
            $adPlaced = $data['applicationOperatingCentre']['adPlacedDate'];
            $formattedDate = $adPlaced['year'] . '-' . $adPlaced['month'] . '-' . $adPlaced['day'];
            $data['applicationOperatingCentre']['adPlacedDate'] = $formattedDate;
        }

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
                    LicenceEntityService::NORTHERN_IRELAND_TRAFFIC_AREA_CODE
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
        if (!isset($data['applicationOperatingCentre']['file']['list'])) {
            return;
        }

        foreach ($data['applicationOperatingCentre']['file']['list'] as $file) {
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

        //$results = $this->getFormTableData($this->getIdentifier(), '');
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
                array($data['data']['minVehicleAuth'], $row['noOfVehiclesPossessed'])
            );

            $data['data']['minTrailerAuth'] = max(
                array($data['data']['minTrailerAuth'], $row['noOfTrailersPossessed'])
            );

            $data['data']['maxVehicleAuth'] += (int)$row['noOfVehiclesPossessed'];
            $data['data']['maxTrailerAuth'] += (int)$row['noOfTrailersPossessed'];
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
        $lvaEntity = 'Entity\\' . ucfirst($this->lva) . 'OperatingCentre';
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
        $form->get('data')->remove('noOfTrailersPossessed');
        $form->getInputFilter()->get('data')->remove('noOfTrailersPossessed');
        $form->remove('advertisements');
        $form->getInputFilter()->remove('advertisements');

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
        $tableData = $this->getTableData();

        // Make the same form alterations that are required for the summary section
        $form = $this->makeFormAlterations($form, $this->getAlterFormOptions());

        if (empty($tableData)) {
            $form->remove('dataTrafficArea');
            return $form;
        }

        $trafficArea = $this->getTrafficArea();
        $trafficAreaId = $trafficArea ? $trafficArea['id'] : '';

        $dataTrafficAreaFieldset = $form->get('dataTrafficArea');

        if ($trafficAreaId) {

            $nameExistsElement = $dataTrafficAreaFieldset->remove('trafficArea')->get('trafficAreaInfoNameExists');

            $nameExistsElement->setValue(
                str_replace('%NAME%', $trafficArea['name'], $nameExistsElement->getValue())
            );
            return $form;
        }
        $options = $this->getServiceLocator()
            ->get('Entity\Licence')->getTrafficAreaValueOptions();

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

        $table->removeColumn('noOfTrailersPossessed');

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

        $this->removeFormFields($form, $fieldsetMap['data'], $removeFields);
    }

    /**
     * Add variation info message
     */
    protected function addVariationInfoMessage()
    {
        $params = [
            'id' => $this->getIdentifier()
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

        $category = $categoryService->getCategoryByDescription('Licensing');
        $subCategory = $categoryService->getCategoryByDescription('Advertisement', 'Document');

        $this->uploadFile(
            $file,
            array_merge(
                array(
                    'description' => 'Advertisement',
                    'category' => $category['id'],
                    'documentSubCategory' => $subCategory['id'],
                ),
                $this->getDocumentProperties()
            )
        );
    }

    public function getDocuments()
    {
        $lvaEntity = 'Entity\\' . ucfirst($this->lva);
        $lvaOcEntity = 'Entity\\' . ucfirst($this->lva) . 'OperatingCentre';

        if (($id = $this->params('child_id')) !== null) {
            $data = $this->getServiceLocator()->get($lvaOcEntity)->getAddressData($id);
            $operatingCentreId = $data['operatingCentre']['id'];
        } else {
            $operatingCentreId = null;
        }

        $documents = $this->getServiceLocator()->get($lvaEntity)
            ->getDocuments($this->getIdentifier(), 'Licensing', 'Advertisement');

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
}

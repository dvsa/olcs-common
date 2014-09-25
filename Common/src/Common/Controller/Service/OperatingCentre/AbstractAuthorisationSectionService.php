<?php

/**
 * Abstract Authorisation Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Service\OperatingCentre;

use Zend\Form\Form;
use Common\Controller\Service\AbstractSectionService;
use Common\Controller\Service\TrafficAreaSectionService;
use Common\Controller\Service\LicenceSectionService;

/**
 * Abstract Authorisation Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractAuthorisationSectionService extends AbstractSectionService
{
    /**
     * OC Count Bundle
     *
     * @var array
     */
    protected $ocCountBundle = array(
        'properties' => array('id')
    );

    /**
     * Action identifier
     *
     * @var string
     */
    protected $actionIdentifier;

    /**
     * Data bundle
     *
     * @var array
     */
    protected $dataBundle;

    /**
     * Table data bundle
     *
     * @var array
     */
    protected $tableDataBundle = array(
        'properties' => array(
            'id',
            'permission',
            'adPlaced',
            'noOfVehiclesPossessed',
            'noOfTrailersPossessed'
        ),
        'children' => array(
            'operatingCentre' => array(
                'properties' => array(
                    'id',
                    'version'
                ),
                'children' => array(
                    'address' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'addressLine1',
                            'addressLine2',
                            'addressLine3',
                            'addressLine4',
                            'postcode',
                            'town'
                        ),
                        'children' => array(
                            'countryCode' => array(
                                'properties' => array(
                                    'id'
                                )
                            )
                        )
                    ),
                    'adDocuments' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'filename',
                            'identifier',
                            'size'
                        )
                    )
                )
            )
        )
    );

    /**
     * Holds the actionDataBundle
     *
     * @var array
     */
    protected $actionDataBundle = array(
        'properties' => array(
            'id',
            'version',
            'noOfTrailersPossessed',
            'noOfVehiclesPossessed',
            'sufficientParking',
            'permission',
            'adPlaced',
            'adPlacedIn',
            'adPlacedDate'
        ),
        'children' => array(
            'operatingCentre' => array(
                'properties' => array(
                    'id',
                    'version'
                ),
                'children' => array(
                    'address' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'addressLine1',
                            'addressLine2',
                            'addressLine3',
                            'addressLine4',
                            'postcode',
                            'town'
                        ),
                        'children' => array(
                            'countryCode' => array(
                                'properties' => array(
                                    'id'
                                )
                            )
                        )
                    ),
                    'adDocuments' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'filename',
                            'identifier',
                            'size'
                        )
                    )
                )
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
     * Form tables name
     *
     * @var string
     */
    protected $formTables = array(
        'table' => 'authorisation_in_form'
    );

    /**
     * Cache the table data
     *
     * @var array
     */
    protected $tableData;

    /**
     * Get document bundle
     *
     * @var array
     */
    protected $getDocumentBundle = array(
        'properties' => array(
            'id',
            'version',
            'identifier',
            'filename',
            'size'
        )
    );

    /**
     * Get form table data
     *
     * @return array
     */
    public function getFormTableData($id, $table)
    {
        if ($this->tableData === null) {

            $data = $this->getDataFromActionService($id, $this->getTableDataBundle());

            $this->tableData = $this->formatSummaryTableData($data);
        }

        return $this->tableData;
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
     * Get traffic area
     *
     * @return string
     */
    public function getTrafficArea()
    {
        return $this->getTrafficAreaSectionService()->getTrafficArea();
    }

    /**
     * Remove trailer elements for PSV and set up Traffic Area section
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    public function alterForm(Form $form)
    {
        // Make the same form alterations that are required for the summary section
        $form = $this->makeFormAlterations($form, $this->getAlterFormOptions());

        if (empty($this->tableData)) {
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

        $dataTrafficAreaFieldset->remove('trafficAreaInfoLabelExists')
            ->remove('trafficAreaInfoNameExists')
            ->remove('trafficAreaInfoHintExists')
            ->get('trafficArea')
            ->setValueOptions($this->getTrafficAreaSectionService()->getTrafficAreaValueOptions());

        return $form;
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
     * Save the data
     *
     * @param array $data
     * @param string $service
     * @return mixed
     */
    public function save($data, $service = null)
    {
        if (isset($data['trafficArea']) && $data['trafficArea']) {
            $this->getSectionService('TrafficArea')->setTrafficArea($data['trafficArea']);
        }

        return parent::save($data, $service);
    }

    /**
     * Generic pricess load method
     *
     * @param array $data
     */
    public function processLoad($data)
    {
        $data['data'] = $oldData = $data;

        $results = $this->getFormTableData($this->getIdentifier(), '');

        $data['data']['noOfOperatingCentres'] = count($results);
        $data['data']['minVehicleAuth'] = 0;
        $data['data']['maxVehicleAuth'] = 0;
        $data['data']['minTrailerAuth'] = 0;
        $data['data']['maxTrailerAuth'] = 0;
        $data['data']['licenceType'] = $this->getLicenceType();

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

    /**
     * Save the operating centre
     *
     * @param array $data
     * @param string $service
     * @return null|Response
     */
    public function actionSave($data, $service = null)
    {
        $saved = parent::actionSave($data['operatingCentre'], 'OperatingCentre');

        if ($this->getActionName() == 'add') {

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
        $saved = parent::actionSave($data['applicationOperatingCentre'], $service);

        if ($this->getActionName() == 'add' && !isset($saved['id'])) {
            throw new \Exception('Unable to save operating centre');
        }

        // set default Traffic Area if we don't have one
        if (!isset($data['trafficArea']) || empty($data['trafficArea']['id'])) {
            $this->setDefaultTrafficArea($data);
        }
    }

    /**
     * Process the action load data
     *
     * @param array $oldData
     */
    public function processActionLoad($oldData)
    {
        $data['data'] = $oldData;

        if ($this->getActionName() != 'add') {
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

        $data['data']['application'] = $this->getIdentifier();
        $trafficArea = $this->getTrafficArea();

        if (is_array($trafficArea) && array_key_exists('id', $trafficArea)) {
            $data['trafficArea']['id'] = $trafficArea['id'];
        }

        return $data;
    }

    /**
     * Get operating centres count
     *
     * @return int
     */
    protected function getOperatingCentresCount()
    {
        $operatingCentres = $this->getDataFromActionService($this->getIdentifier(), $this->ocCountBundle);

        return $operatingCentres['Count'];
    }

    /**
     * Shared logic to get data from action service
     *
     * @param int $id
     * @param array $bundle
     * @return array
     */
    protected function getDataFromActionService($id, $bundle)
    {
        return $this->makeRestCall(
            $this->getActionService(),
            'GET',
            array($this->actionIdentifier => $id),
            $bundle
        );
    }

    /**
     * Format summary table data
     *
     * @param array $data
     * @return array
     */
    protected function formatSummaryTableData($data)
    {
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

        return $newData;
    }

    /**
     * Alter action form for PSV licences
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterActionFormForPsv(Form $form)
    {
        $form->get('data')->remove('noOfTrailersPossessed');
        $form->remove('advertisements');

        $dataLabel = $form->get('data')->getLabel();
        $form->get('data')->setLabel($dataLabel . '-psv');

        $parkingLabel = $form->get('data')->get('sufficientParking')->getLabel();
        $form->get('data')->get('sufficientParking')->setLabel($parkingLabel . '-psv');

        $permissionLabel = $form->get('data')->get('permission')->getLabel();
        $form->get('data')->get('permission')->setLabel($permissionLabel . '-psv');
    }

    /**
     * Alter the form with all the traffic area stuff
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterFormForTrafficArea(Form $form)
    {
        $licenceData = $this->getLicenceData();
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
     * Check if licence/application is psv
     *
     * @NOTE no need to cache, as this is cached within the licence service
     *
     * @return boolean
     */
    protected function isPsv()
    {
        return $this->getLicenceSectionService()->isPsv();
    }

    /**
     * Get licence data
     *
     * @NOTE no need to cache, as this is cached within the licence service
     *
     * @return array
     */
    protected function getLicenceData()
    {
        return $this->getLicenceSectionService()->getLicenceData();
    }

    /**
     * Get licence type
     *
     * @NOTE no need to cache, as this is cached within the licence service
     *
     * @return string
     */
    protected function getLicenceType()
    {
        return $this->getLicenceSectionService()->getLicenceType();
    }

    /**
     * Get the traffic area section service
     *
     * @return \Common\Controller\Service\TrafficAreaSectionService
     */
    protected function getTrafficAreaSectionService()
    {
        return $this->getSectionService('TrafficArea');
    }

    /**
     * Get the alter form options
     *
     * @return array
     */
    protected function getAlterFormOptions()
    {
        return array(
            'isPsv' => $this->isPsv(),
            'isReview' => false,
            'data' => array(
                'licence' => array(
                    'licenceType' => array(
                        'id' => $this->getLicenceType()
                    )
                )
            )
        );
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
            LicenceSectionService::LICENCE_TYPE_STANDARD_NATIONAL,
            LicenceSectionService::LICENCE_TYPE_STANDARD_INTERNATIONAL
        );

        $allowCommunityLicences = array(
            LicenceSectionService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
            LicenceSectionService::LICENCE_TYPE_RESTRICTED
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
     * Set the default traffic area
     *
     * @param array $data
     */
    protected function setDefaultTrafficArea($data)
    {
        $licenceData = $this->getLicenceData();

        if ($licenceData['niFlag'] == 'Y') {
            $this->getSectionService('TrafficArea')->setTrafficArea(
                TrafficAreaSectionService::NORTHERN_IRELAND_TRAFFIC_AREA_CODE
            );
            return;
        }

        $postcode = $data['operatingCentre']['addresses']['address']['postcode'];

        if (!empty($postcode) && $this->getOperatingCentresCount() == 1) {

            $postcodeService = $this->getPostcodeService();

            $trafficAreaParts = $postcodeService->getTrafficAreaByPostcode($postcode);

            if (!empty($trafficAreaParts)) {
                $this->getSectionService('TrafficArea')->setTrafficArea(array_shift($trafficAreaParts));
            }
        }
    }

    /**
     * Get postcode service
     *
     * @return Common\Service\Postcode\Postcode
     */
    protected function getPostcodeService()
    {
        return $this->getServiceLocator()->get('postcode');
    }

    /**
     * Save the documents
     *
     * @param array $data
     * @param int $operatingCentreId
     */
    protected function saveDocuments($data, $operatingCentreId)
    {
        if (isset($data['applicationOperatingCentre']['file']['list'])) {
            foreach ($data['applicationOperatingCentre']['file']['list'] as $file) {
                $this->makeRestCall(
                    'Document',
                    'PUT',
                    array('id' => $file['id'], 'version' => $file['version'], 'operatingCentre' => $operatingCentreId)
                );
            }
        }
    }

    /**
     * Alter action form for goods licences
     *
     * @param \Zend\Form\Form
     */
    abstract protected function alterActionFormForGoods(Form $form);

    /**
     * Get licence section service
     *
     * @return \Common\Controller\Service\SectionServiceInterface
     */
    abstract protected function getLicenceSectionService();
}

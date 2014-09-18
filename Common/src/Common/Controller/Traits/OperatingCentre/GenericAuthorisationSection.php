<?php

/**
 * Generic Authorisation Section
 *
 * Internal/External - Application/Licence Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\OperatingCentre;

use Common\Controller\Traits;

/**
 * Generic Authorisation Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait GenericAuthorisationSection
{
    use Traits\TrafficAreaTrait;

    protected $ocCountBundle = array(
        'properties' => array('id')
    );

    /**
     * Holds the table data
     *
     * @var array
     */
    protected $tableData = null;

    /**
     * Table data bundle
     *
     * @var array
     */
    public static $tableDataBundle = array(
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
     * Data map
     *
     * @var array
     */
    protected $sharedDataMap = array(
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
    protected $sharedFormTables = array('table' => 'authorisation_in_form');

    /**
     * Action data map
     *
     * @var array
     */
    protected $sharedActionDataMap = array(
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
     * Holds the actionDataBundle
     *
     * @var array
     */
    protected $sharedActionDataBundle = array(
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
     * Get the action data bundle
     *
     * @return array
     */
    protected function getActionDataBundle()
    {
        return $this->sharedActionDataBundle;
    }

    /**
     * Get the action data map
     *
     * @return array
     */
    protected function getActionDataMap()
    {
        return $this->sharedActionDataMap;
    }

    /**
     * Get action service
     *
     * @return string
     */
    protected function getActionService()
    {
        return $this->sharedActionService;
    }

    /**
     * Get data map
     *
     * @return array
     */
    protected function getDataMap()
    {
        return $this->sharedDataMap;
    }

    /**
     * Get form tables
     *
     * @return array
     */
    protected function getFormTables()
    {
        return $this->sharedFormTables;
    }

    /**
     * Get data bundle
     *
     * @return array
     */
    protected function getDataBundle()
    {
        return $this->sharedDataBundle;
    }

    /**
     * Get service
     *
     * @return type
     */
    protected function getService()
    {
        return $this->sharedService;
    }

    /**
     * Render the section form
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->renderSection();
    }

    /**
     * Add operating centre
     */
    public function addAction()
    {
        return $this->renderSection();
    }

    /**
     * Edit operating centre
     */
    public function editAction()
    {
        return $this->renderSection();
    }

    /**
     * Delete sub action
     *
     * @return Response
     */
    public function deleteAction()
    {
        $this->maybeClearTrafficAreaId();
        return $this->delete();
    }

    /**
     * Get data for table
     *
     * @param string $id
     */
    protected function getFormTableData($id, $table)
    {
        if (is_null($this->tableData)) {
            $this->tableData = $this->getSummaryTableData($id, $this, '');
        }

        return $this->tableData;
    }

    /**
     * Format summary table data
     *
     * @param array $data
     * @return array
     */
    protected static function formatSummaryTableData($data)
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
     * Clear Traffic Area if we are deleting last one operating centres
     */
    protected function maybeClearTrafficAreaId()
    {
        $ocCount = $this->getOperatingCentresCount();

        if ($ocCount === 1 && $this->getActionId()) {
            $this->setTrafficArea(null);
        }
    }

    /**
     * Remove trailer elements for PSV and set up Traffic Area section
     *
     * @param object $form
     * @return object
     */
    protected function alterForm($form)
    {
        // Make the same form alterations that are required for the summary section
        $form = $this->makeFormAlterations($form, $this, $this->getAlterFormOptions());

        // set up Traffic Area section
        $operatingCentresExists = !empty($this->tableData);
        $trafficArea = $this->getTrafficArea();
        $trafficAreaId = $trafficArea ? $trafficArea['id'] : '';

        if (!$operatingCentresExists) {
            $form->remove('dataTrafficArea');
        } elseif ($trafficAreaId) {
            $form->get('dataTrafficArea')->remove('trafficArea');
            $template = $form->get('dataTrafficArea')->get('trafficAreaInfoNameExists')->getValue();
            $newValue = str_replace('%NAME%', $trafficArea['name'], $template);
            $form->get('dataTrafficArea')->get('trafficAreaInfoNameExists')->setValue($newValue);
        } else {
            $form->get('dataTrafficArea')->remove('trafficAreaInfoLabelExists');
            $form->get('dataTrafficArea')->remove('trafficAreaInfoNameExists');
            $form->get('dataTrafficArea')->remove('trafficAreaInfoHintExists');
            $form->get('dataTrafficArea')->get('trafficArea')->setValueOptions($this->getTrafficValueOptions());
        }

        return $form;
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
                'data' => array(
                    'licence' => array(
                        'licenceType' => array(
                            'id' => $this->getLicenceType()
                        )
                    )
                )
            )
        );
    }

    /**
     * Make form alterations
     *
     * This method enables the summary to apply the same form alterations. In this
     * case we ensure we manipulate the form based on whether the license is PSV or not
     *
     * @param Form $form
     * @param mixed $context
     * @param array $options
     *
     * @return $form
     */
    public static function makeFormAlterations($form, $context, $options = array())
    {
        $fieldsetMap = static::getFieldsetMap($form, $options);

        if ($options['isPsv']) {
            static::alterFormForPsvLicences($form, $fieldsetMap, $options);
            static::alterFormTableForPsv($form, $fieldsetMap);
        } else {
            static::alterFormForGoodsLicences($form, $fieldsetMap);
        }

        if ($options['isReview']) {
            static::alterFormForReview($form, $fieldsetMap, $context, $options);
        }

        return $form;
    }

    /**
     * Alter form for GOODS applications
     *
     * @param \Zend\Form\Form $form
     * @param array $fieldsetMap
     */
    protected static function alterFormForGoodsLicences($form, $fieldsetMap)
    {
        $removeFields = array(
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles',
            'totCommunityLicences'
        );

        static::removeFormFields($form, $fieldsetMap['data'], $removeFields);
    }

    /**
     * Alter form for PSV applications
     *
     * @param \Zend\Form\Form $form
     * @param array $fieldsetMap
     * @param array $options
     */
    protected static function alterFormForPsvLicences($form, $fieldsetMap, $options)
    {
        static::alterFormHintForPsv($form, $fieldsetMap);

        $removeFields = array(
            'totAuthVehicles',
            'totAuthTrailers',
            'minTrailerAuth',
            'maxTrailerAuth'
        );

        $licenceType = $options['data']['data']['licence']['licenceType']['id'];

        $allowLargeVehicles = array(static::LICENCE_TYPE_STANDARD_NATIONAL, static::LICENCE_TYPE_STANDARD_INTERNATIONAL);

        $allowCommunityLicences = array(static::LICENCE_TYPE_STANDARD_INTERNATIONAL, static::LICENCE_TYPE_RESTRICTED);

        if (!in_array($licenceType, $allowLargeVehicles)) {
            $removeFields[] = 'totAuthLargeVehicles';
        }

        if (!in_array($licenceType, $allowCommunityLicences)) {
            $removeFields[] = 'totCommunityLicences';
        }

        static::removeFormFields($form, $fieldsetMap['data'], $removeFields);
    }

    /**
     * Alter form table for PSV
     *
     * @param \Zend\Form\Form $form
     * @param array $fieldsetMap
     */
    protected static function alterFormTableForPsv($form, $fieldsetMap)
    {
        $table = $form->get($fieldsetMap['table'])->get('table')->getTable();

        $table->removeColumn('trailersCol');

        $footer = $table->getFooter();
        $footer['total']['content'] .= '-psv';
        unset($footer['trailersCol']);
        $table->setFooter($footer);
    }

    /**
     * Remove a list of form fields
     *
     * @param \Zend\Form\Form $form
     * @param string $fieldset
     * @param array $fields
     */
    protected static function removeFormFields($form, $fieldset, array $fields)
    {
        foreach ($fields as $field) {
            $form->get($fieldset)->remove($field);
        }
    }

    /**
     * Alter form hint for psv
     *
     * @param \Zend\Form\Form $form
     * @param array $fieldsetMap
     */
    protected static function alterFormHintForPsv($form, $fieldsetMap)
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
    protected static function getFieldsetMap($form, $options)
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
}

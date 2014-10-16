<?php

/**
 * Shared logic between Operating Centres controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Traits\Lva;

use Zend\Form\Form;

/**
 * Shared logic between Operating Centres controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
trait OperatingCentresTrait
{
    use GenericLvaTrait,
        CrudTableTrait;

    protected $tableData = array();

    /**
     * Needed by the Crud Table Trait
     */
    private $section = 'operating_centres';

    abstract protected function alterForm($form);

    abstract protected function isPsv();

    abstract protected function getLicenceId($lvaId = null);

    /**
     * Index action
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            // @TODO this is wrong; the data fetched depends on LVA type (I think)
            $data = $this->getServiceLocator()->get('Entity\Application')
                ->getOperatingCentresData($this->getApplicationId());
        }

        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createForm('Lva\OperatingCentres');

        $form = $this->alterForm($form)
            ->setData($data);

        $table = $this->getServiceLocator()
            ->get('Table')
            ->prepareTable(
                'authorisation_in_form',
                $this->getTableData()
            );

        $column = $table->getColumn('address');
        $column['type'] = $this->lva;
        $table->setColumn('address', $column);

        $form->get('table')  // fieldset
            ->get('table')   // element
            ->setTable($table);

        if ($request->isPost() && $form->isValid()) {
            //
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

            $trafficAreaSection = $this->createSectionService('TrafficArea');
            $trafficAreaSection->setIdentifier($options['data']['id']);
            $trafficArea = $trafficAreaSection->getTrafficArea();

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
                        'id' => $this->getLicenceId()
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
        $id = $this->getApplicationId();

        // @TODO not in common trait... currently always fetches from app operating centre
        $data = $this->getServiceLocator()->get('Entity\ApplicationOperatingCentre')
            ->getAddressData($id);

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

    public function addAction()
    {
    }

    public function editAction()
    {
    }

    protected function delete()
    {

    }

    protected function formatCrudDataForSave($data)
    {
        return $data;
    }

    protected function formatCrudDataForForm($data)
    {
        return $data;
    }
}

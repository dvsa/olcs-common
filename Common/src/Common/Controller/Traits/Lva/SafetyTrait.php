<?php

/**
 * Safety Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\Lva;

use Common\Service\Helper\FormHelperService;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Entity\ContactDetailsEntityService;

/**
 * Safety Trait
 *
 * @NOTE this is being built for application first
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait SafetyTrait
{
    use CrudTableTrait;

    protected $section = 'safety';

    /**
     * Shared action data map
     *
     * @var array
     */
    protected $safetyProvidersActionDataMap = array(
        '_addresses' => array(
            'address'
        ),
        'main' => array(
            'children' => array(
                'workshop' => array(
                    'mapFrom' => array(
                        'data'
                    )
                ),
                'contactDetails' => array(
                    'mapFrom' => array(
                        'contactDetails'
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
     * Redirect to the first section
     *
     * @return Response
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->formatDataForForm($this->getSafetyData());
        }

        $typeOfLicence = $this->getTypeOfLicenceData();

        $form = $this->alterForm($this->getSafetyForm(), $typeOfLicence['goodsOrPsv'])->setData($data);

        if ($request->isPost()) {

            $crudAction = $this->getCrudAction(array($data['table']));

            if ($crudAction !== null) {
                $this->getServiceLocator()->get('Helper\Form')->disableEmptyValidation($form);
            }

            if ($form->isValid()) {

                $this->save($data);

                if ($crudAction !== null) {
                    return $this->handleCrudAction($crudAction);
                }

                return $this->completeSection('safety');
            }
        }

        return $this->render('safety', $form);
    }

    /**
     * Add person action
     */
    public function addAction()
    {
        return $this->addOrEdit('add');
    }

    /**
     * Edit person action
     */
    public function editAction()
    {
        return $this->addOrEdit('edit');
    }

    /**
     * Delete
     */
    protected function delete()
    {

    }

    /**
     * Helper method as both add and edit pretty
     * much do the same thing
     *
     * @param string $mode
     */
    protected function addOrEdit($mode)
    {
        $request = $this->getRequest();
        $safetyProviderData = array();
        $data = array();
        $id = $this->params('child_id');

        if ($mode !== 'add') {
            $safetyProviderData = $this->getSafetyProviderData($id, $mode);
        }

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } elseif ($mode === 'edit') {
            $data = $this->formatCrudDataForForm($safetyProviderData, $mode);
        }

        $form = $this->getSafetyProviderForm()->setData($data);

        // @todo this could do with drying up
        if ($mode !== 'add') {
            $form->get('form-actions')->remove('addAnother');
        }

        $addressLookup = $this->getServiceLocator()->get('Helper\Form')->processAddressLookupForm($form, $request);

        if (!$addressLookup && $request->isPost() && $form->isValid()) {

            list($contactDetails, $workshop) = $this->formatCrudDataForSave($data);

            if ($mode === 'edit') {
                $workshop['id'] = $id;
            }

            $workshop['licence'] = $this->getLicenceId();
            $workshop['contactDetails'] = $this->saveContactDetails($contactDetails, $safetyProviderData, $mode);

            $this->saveWorkshop($workshop);

            return $this->handlePostSave();
        }

        return $this->render($mode . '_safety', $form);
    }

    /**
     * Format crud data for saving
     *
     * @param array $data
     * @return array
     */
    protected function formatCrudDataForSave($data)
    {
        $processedData = $this->getServiceLocator()->get('Helper\Data')
            ->processDataMap($data, $this->safetyProvidersActionDataMap);

        $processedData['contactDetails']['contactType'] = ContactDetailsEntityService::CONTACT_TYPE_WORKSHOP;

        return array($processedData['contactDetails'], $processedData['workshop']);
    }

    /**
     * Save contact details
     *
     * @param array $data
     * @param array $safetyProviderData
     * @param string $mode
     * @return int
     */
    protected function saveContactDetails($data, $safetyProviderData, $mode)
    {
        if ($mode === 'edit') {
            $data['id'] = $safetyProviderData['contactDetails']['id'];
        }

        $result = $this->getServiceLocator()->get('Entity\ContactDetails')->save($data);

        if ($mode === 'add') {
            return $result['id'];
        }

        return $data['id'];
    }

    /**
     * Save workshop
     *
     * @param array $data
     */
    protected function saveWorkshop($data)
    {
        $this->getServiceLocator()->get('Entity\Workshop')->save($data);
    }

    /**
     * Format data for the safety providers table
     *
     * @param array $data
     * @param string $mode
     * @return array
     */
    protected function formatCrudDataForForm($data, $mode)
    {
        if ($mode == 'edit') {
            $data['data'] = array(
                'version' => $data['version'],
                'isExternal' => $data['isExternal']
            );

            $data['address'] = $data['contactDetails']['address'];
            $data['address']['countryCode'] = $data['address']['countryCode']['id'];

            unset($data['version']);
            unset($data['isExternal']);
            unset($data['contactDetails']['address']);
        }

        return $data;
    }

    /**
     * Get safety provider form
     *
     * @return \Zend\Form\Form
     */
    protected function getSafetyProviderForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\SafetyProviders');
    }

    /**
     * Get safety provider data
     *
     * @return array
     */
    protected function getSafetyProviderData($id)
    {
        return $this->getServiceLocator()->get('Entity\Workshop')->getById($id);
    }

    /**
     * Save the form data
     *
     * @param array $data
     */
    protected function save($data)
    {
        list($licence, $application) = $this->formatSaveData($data);

        $licence['id'] = $this->getLicenceId();
        $application['id'] = $this->getApplicationId();

        $this->getServiceLocator()->get('Entity\Licence')->save($licence);
        $this->getServiceLocator()->get('Entity\Application')->save($application);
    }

    /**
     * Shared logic to save licence
     *
     * @param array $data
     * @return array
     */
    protected function formatSaveData($data)
    {
        $data['licence']['safetyInsVehicles'] = str_replace(
            'inspection_interval_vehicle.',
            '',
            $data['licence']['safetyInsVehicles']
        );

        if (isset($data['licence']['safetyInsTrailers'])) {
            $data['licence']['safetyInsTrailers'] = str_replace(
                'inspection_interval_trailer.',
                '',
                $data['licence']['safetyInsTrailers']
            );
        }

        // Need to explicitly set these to null, otherwise empty string gets converted to 0
        if (array_key_exists('safetyInsTrailers', $data['licence']) && empty($data['licence']['safetyInsTrailers'])) {
            $data['licence']['safetyInsTrailers'] = null;
        }

        if (array_key_exists('safetyInsVehicles', $data['licence']) && empty($data['licence']['safetyInsVehicles'])) {
            $data['licence']['safetyInsVehicles'] = null;
        }

        return array($data['licence'], $data['application']);
    }

    /**
     * Alter form
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterForm($form, $goodsOrPsv)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        // This element needs to be visible internally
        $formHelper->remove($form, 'application->isMaintenanceSuitable');

        if ($goodsOrPsv == LicenceEntityService::LICENCE_CATEGORY_PSV) {

            $formHelper->remove($form, 'licence->safetyInsTrailers');

            $formHelper->alterElementLabel(
                $form->get('licence')->get('safetyInsVaries'),
                '.psv',
                FormHelperService::ALTER_LABEL_APPEND
            );

            $table = $form->get('table')->get('table')->getTable();

            $emptyMessage = $table->getVariable('empty_message');
            $table->setVariable('empty_message', $emptyMessage . '-psv');

            $form->get('table')->get('table')->setTable($table);
        }

        return $form;
    }

    /**
     * Format data for form
     *
     * @param array $data
     */
    protected function formatDataForForm($data)
    {
        if (isset($data['licence']['tachographIns']['id'])) {
            $data['licence']['tachographIns'] = $data['licence']['tachographIns']['id'];
        }

        $data['application'] = array(
            'version' => $data['version'],
            'safetyConfirmation' => $data['safetyConfirmation'],
            'isMaintenanceSuitable' => $data['isMaintenanceSuitable']
        );

        unset($data['version']);
        unset($data['safetyConfirmation']);
        unset($data['isMaintenanceSuitable']);

        $data['licence']['safetyInsVehicles'] = 'inspection_interval_vehicle.' . $data['licence']['safetyInsVehicles'];
        $data['licence']['safetyInsTrailers'] = 'inspection_interval_trailer.' . $data['licence']['safetyInsTrailers'];

        return $data;
    }

    /**
     * Get Safety Data
     *
     * @return array
     */
    protected function getSafetyData()
    {
        return $this->getServiceLocator()->get('Entity\Application')->getSafetyData($this->getApplicationId());
    }

    /**
     * Get safety form
     *
     * @return \Zend\Form\Form
     */
    protected function getSafetyForm()
    {
        $form = $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\Safety');

        $form->get('table')->get('table')->setTable($this->getSafetyTable());

        return $form;
    }

    /**
     * Get safety table
     */
    protected function getSafetyTable()
    {
        return $this->getServiceLocator()->get('Table')->prepareTable('lva-safety', $this->getTableData());
    }

    /**
     * Get table data
     *
     * @return array
     */
    protected function getTableData()
    {
        $data = $this->getServiceLocator()->get('Entity\Workshop')->getForLicence($this->getLicenceId());

        $translatedData = array();

        foreach ($data as $row) {
            $translatedRow = array(
                'isExternal' => $row['isExternal'],
                'id' => $row['id'],
                'fao' => $row['contactDetails']['fao'],
                'addressLine1' => $row['contactDetails']['address']['addressLine1'],
                'addressLine2' => $row['contactDetails']['address']['addressLine2'],
                'addressLine3' => $row['contactDetails']['address']['addressLine3'],
                'addressLine4' => $row['contactDetails']['address']['addressLine4'],
                'town' => $row['contactDetails']['address']['town'],
                'postcode' => $row['contactDetails']['address']['postcode'],
                'countryCode' => array('id' => $row['contactDetails']['address']['countryCode']['id'])
            );

            $translatedData[] = $translatedRow;
        }

        return $translatedData;
    }
}

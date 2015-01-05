<?php

/**
 * Shared logic between Taxi Phv controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Form\Elements\Validators\PrivateHireLicenceTrafficAreaValidator;

/**
 * Shared logic between Taxi Phv controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractTaxiPhvController extends AbstractController
{
    use Traits\CrudTableTrait {
        Traits\CrudTableTrait::handleCrudAction as genericHandleCrudAction;
    }

    protected $tableData;

    protected $section = 'taxi_phv';

    /**
     * Action data map
     *
     * @var array
     */
    protected $licenceDataMap = array(
        '_addresses' => array(
            'address'
        ),
        'main' => array(
            'children' => array(
                'privateHireLicence' => array(
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

    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->getFormData();
        }

        $form = $this->getForm()->setData($data);

        $this->alterFormForLocation($form);
        $this->alterFormForLva($form);

        if ($request->isPost()) {

            $crudAction = $this->getCrudAction(array($data['table']));

            if ($crudAction !== null) {
                $this->getServiceLocator()->get('Helper\Form')->disableEmptyValidation($form);
            }

            if ($form->isValid()) {

                $this->save($data);
                $this->postSave('taxi_phv');

                if ($crudAction !== null) {
                    return $this->handleCrudAction($crudAction);
                }

                return $this->completeSection('taxi_phv');
            }
        }

        $this->getServiceLocator()->get('Script')->loadFile('lva-crud');

        return $this->render('taxi_phv', $form);
    }

    protected function handleCrudAction($data)
    {
        $action = $this->getActionFromCrudAction($data);

        $trafficArea = $this->getServiceLocator()->get('Entity\Licence')->getTrafficArea($this->getLicenceId());

        if (empty($trafficArea)) {
            $dataTrafficArea = $this->params()->fromPost('dataTrafficArea');

            $trafficArea = is_array($dataTrafficArea) && isset($dataTrafficArea['trafficArea'])
                ? $dataTrafficArea['trafficArea'] : '';

            if ($action == 'add' && empty($trafficArea) && $this->getPrivateHireLicencesCount() > 0) {

                $this->getServiceLocator()->get('Helper\FlashMessenger')
                    ->addWarningMessage('Please select a traffic area');

                return $this->reload();

            } elseif ($action == 'add' && !empty($trafficArea)) {
                $this->getServiceLocator()->get('Entity\Licence')
                    ->setTrafficArea($this->getLicenceId(), $trafficArea);
            }
        }

        return $this->genericHandleCrudAction($data);
    }

    /**
     * Save method
     *
     * @param array $data
     */
    protected function save($data)
    {
        if (isset($data['dataTrafficArea']['trafficArea']) && !empty($data['dataTrafficArea']['trafficArea'])) {
            $this->getServiceLocator()->get('Entity\Licence')->setTrafficArea(
                $this->getLicenceId(),
                $data['dataTrafficArea']['trafficArea']
            );
        }
    }

    protected function getForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $formHelper->createForm('Lva\TaxiPhv');

        $formHelper->populateFormTable($form->get('table'), $this->getTable());

        return $this->alterForm($form);
    }

    protected function getFormData()
    {
        return array(
            'dataTrafficArea' => $this->getServiceLocator()->get('Entity\Licence')
                ->getTrafficArea($this->getLicenceId())
        );
    }

    protected function getTable()
    {
        return $this->getServiceLocator()->get('Table')->prepareTable('lva-taxi-phv', $this->getTableData());
    }

    protected function getTableData()
    {
        if ($this->tableData === null) {

            $data = $this->getServiceLocator()->get('Entity\PrivateHireLicence')->getByLicenceId($this->getLicenceId());

            $newData = array();

            foreach ($data as $row) {

                $newRow = array(
                    'id' => $row['id'],
                    'privateHireLicenceNo' => $row['privateHireLicenceNo'],
                    'councilName' => $row['contactDetails']['description']
                );

                unset($row['contactDetails']['address']['id']);
                unset($row['contactDetails']['address']['version']);

                $newData[] = array_merge($newRow, $row['contactDetails']['address']);
            }

            $this->tableData = $newData;
        }

        return $this->tableData;
    }

    protected function alterForm($form)
    {
        $licenceTableData = $this->getTableData();

        $licenceService = $this->getServiceLocator()->get('Entity\Licence');

        $trafficArea = $licenceService->getTrafficArea($this->getLicenceId());

        $trafficAreaId = $trafficArea ? $trafficArea['id'] : '';

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        if (empty($licenceTableData)) {
            $formHelper->remove($form, 'dataTrafficArea');
        } elseif ($trafficAreaId) {
            $formHelper->remove($form, 'dataTrafficArea->trafficArea');
            $template = $form->get('dataTrafficArea')->get('trafficAreaInfoNameExists')->getValue();
            $newValue = str_replace('%NAME%', $trafficArea['name'], $template);
            $form->get('dataTrafficArea')->get('trafficAreaInfoNameExists')->setValue($newValue);
        } else {
            $formHelper->remove($form, 'dataTrafficArea->trafficAreaInfoLabelExists');
            $formHelper->remove($form, 'dataTrafficArea->trafficAreaInfoNameExists');
            $formHelper->remove($form, 'dataTrafficArea->trafficAreaInfoHintExists');

            $form->get('dataTrafficArea')->get('trafficArea')->setValueOptions(
                $this->getServiceLocator()->get('Entity\TrafficArea')->getValueOptions()
            );
        }

        return $form;
    }

    public function addAction()
    {
        return $this->addOrEdit('add');
    }

    public function editAction()
    {
        return $this->addOrEdit('edit');
    }

    protected function addOrEdit($mode)
    {
        $request = $this->getRequest();

        $data = array();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } elseif ($mode === 'edit') {
            $id = $this->params('child_id');
            $data = $this->getLicenceFormData($id);
        }

        if (!$request->isPost()) {
            $data = $this->formatDataForLicenceForm($mode, $data);
        }

        $form = $this->getLicenceForm()->setData($data);

        if ($mode === 'edit') {
            $form->get('form-actions')->remove('addAnother');
        }

        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $hasProcessed = $formHelper->processAddressLookupForm($form, $this->getRequest());
        // (don't validate or proceed if we're just processing the postcode lookup)

        if (!$hasProcessed && $request->isPost() && $form->isValid()) {

            $data = $this->getServiceLocator()->get('Helper\Data')->processDataMap($data, $this->licenceDataMap);

            $this->saveLicence($data);

            return $this->handlePostSave();
        }

        return $this->render($mode . '_taxi_phv', $form);
    }

    protected function getLicenceForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $formHelper->createFormWithRequest('Lva\TaxiPhvLicence', $this->getRequest());
        return $this->alterActionForm($form);
    }

    /**
     * Alter form to process traffic area
     *
     * @param Form $form
     */
    protected function alterActionForm($form)
    {
        $form->getInputFilter()->get('address')->get('postcode')->setRequired(false);

        $trafficArea = $this->getServiceLocator()->get('Entity\Licence')->getTrafficArea($this->getLicenceId());

        $trafficAreaValidator = new PrivateHireLicenceTrafficAreaValidator();
        $trafficAreaValidator->setServiceLocator($this->getServiceLocator());

        $trafficAreaValidator->setPrivateHireLicencesCount(
            $this->getPrivateHireLicencesCount()
        );

        $trafficAreaValidator->setTrafficArea($trafficArea);

        $postcodeValidatorChain = $form->getInputFilter()->get('address')->get('postcode')->getValidatorChain();
        $postcodeValidatorChain->attach($trafficAreaValidator);

        if (!$trafficArea && $form->get('form-actions')->has('addAnother')) {
            $form->get('form-actions')->remove('addAnother');
        }
        return $form;
    }

    protected function getLicenceFormData($id)
    {
        return $this->getServiceLocator()->get('Entity\PrivateHireLicence')->getById($id);
    }

    /**
     * Delete licence
     *
     * @return Response
     */
    public function delete()
    {
        $ids = explode(',', $this->params('child_id'));
        $licCount = $this->getPrivateHireLicencesCount();

        if (count($ids) === $licCount) {
            $this->getServiceLocator()
                ->get('Entity\Licence')
                ->setTrafficArea($this->getLicenceId(), null);
        }

        $service = $this->getServiceLocator()->get('Entity\PrivateHireLicence');

        foreach ($ids as $id) {
            $service->delete($id);
        }
    }

    protected function getPrivateHireLicencesCount($licenceId = null)
    {
        if ($licenceId === null) {
            $licenceId = $this->getLicenceId();
        }
        return $this->getServiceLocator()->get('Entity\PrivateHireLicence')->getCountByLicence($licenceId);
    }

    /**
     * Process the action load data
     *
     * @param array $oldData
     */
    protected function formatDataForLicenceForm($mode, $oldData)
    {
        $data['data'] = $oldData;

        if ($mode !== 'add') {
            $data['contactDetails'] = $oldData['contactDetails'];
            $data['address'] = $oldData['contactDetails']['address'];
            $data['address']['countryCode'] = $data['address']['countryCode']['id'];
        }

        $trafficArea = $this->getServiceLocator()->get('Entity\Licence')->getTrafficArea($this->getLicenceId());

        if (isset($trafficArea['id'])) {
            $data['trafficArea'] = $trafficArea['id'];
        }

        return $data;
    }

    /**
     * Save the licence
     *
     * @param array $data
     * @param string $service
     * @return null|Response
     */
    protected function saveLicence($data)
    {
        $data['contactDetails']['contactType'] = 'ct_council';

        $results = $this->getServiceLocator()->get('Entity\ContactDetails')->save($data['contactDetails']);

        if (!empty($data['contactDetails']['id'])) {
            $contactDetailsId = $data['contactDetails']['id'];
        } elseif (isset($results['id'])) {
            $contactDetailsId = $results['id'];
        } else {
            /**
             * Handle failure to save contactDetails. For now we just throw an exception until the story has been
             * complete which encompassess feeding back errors to the user
             */
            throw new \Exception('Unable to save contact details');
        }

        $data['privateHireLicence']['contactDetails'] = $contactDetailsId;

        $id = $this->params('child_id');

        if (!empty($id)) {
            $data['privateHireLicence']['id'] = $id;
        }

        $licenceId = $this->getLicenceId();

        $data['privateHireLicence']['licence'] = $licenceId;

        $this->getServiceLocator()->get('Entity\PrivateHireLicence')->save($data['privateHireLicence']);

        // set default Traffic Area if we don't have one
        if (!isset($data['trafficArea'])
            || empty($data['trafficArea']['id'])
            && $data['contactDetails']['addresses']['address']['postcode']
        ) {

            $licencesCount = $this->getPrivateHireLicencesCount();

            // first Licence was just added or we are editing the first one
            if ($licencesCount === 1) {

                $postcodeService = $this->getServiceLocator()->get('postcode');

                $trafficAreaId = $postcodeService->getTrafficAreaByPostcode(
                    $data['contactDetails']['addresses']['address']['postcode']
                )[0];

                if (!empty($trafficAreaId)) {
                    $this->getServiceLocator()->get('Entity\Licence')->setTrafficArea($licenceId, $trafficAreaId);
                }
            }
        }
    }
}

<?php

/**
 * Shared logic for Goods *AND* PSV controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Form\Elements\Validators\NewVrm;
use Zend\Form\Element\Checkbox;

/**
 * Shared logic for Goods *AND* PSV controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractVehiclesController extends AbstractController
{
    protected $totalAuthorisedVehicles;
    protected $totalVehicles;

    /**
     * Action data map
     *
     * @var array
     */
    protected $vehicleDataMap = array(
        'main' => array(
            'mapFrom' => array(
                'data'
            ),
            'children' => array(
                'licence-vehicle' => array(
                    'mapFrom' => array(
                        'licence-vehicle'
                    )
                )
            )
        )
    );

    /**
     * We need to know which vehicles to show
     *
     * @param array $licenceVehicle
     * @return boolean
     */
    abstract protected function showVehicle(array $licenceVehicle);

    protected function alterVehicleFormForLocation($form, $mode)
    {
        return $form;
    }

    /**
     * Redirect to the first section
     *
     * @return Response
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $this->postSave('vehicles');

            $data = (array)$request->getPost();
            $crudAction = $this->getCrudAction(array($data['table']));

            if ($crudAction !== null) {

                $action = $this->getActionFromCrudAction($crudAction);

                $alternativeCrudAction = $this->checkForAlternativeCrudAction($action);

                if ($alternativeCrudAction === null) {
                    return $this->handleCrudAction($crudAction, array('add', 'print-vehicles'));
                }

                return $alternativeCrudAction;
            }

            return $this->completeSection('vehicles');
        }

        $form = $this->getForm();

        $this->getServiceLocator()->get('Script')->loadFile('lva-crud');

        if ($this->getTotalNumberOfVehicles() > $this->getTotalNumberOfAuthorisedVehicles()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addWarningMessage(
                'more-vehicles-than-authorisation'
            );
        }

        return $this->render('vehicles', $form);
    }

    /**
     * Hijack the crud action check so we can validate the add button
     *
     * @param string $action
     */
    protected function checkForAlternativeCrudAction($action)
    {
        if ($action == 'reprint') {
            $post = (array)$this->getRequest()->getPost();

            $id = $post['table']['id'];

            if ($this->isDiscPendingForLicenceVehicle($id)) {

                $this->getServiceLocator()->get('Helper\FlashMessenger')
                    ->addErrorMessage('reprint-pending-disc-error');

                return $this->reload();
            }
        }

        if ($action == 'add') {
            $totalAuth = $this->getTotalNumberOfAuthorisedVehicles();

            if (!is_numeric($totalAuth)) {
                return;
            }

            $vehicleCount = $this->getTotalNumberOfVehicles();

            if ($vehicleCount >= $totalAuth) {

                $this->getServiceLocator()->get('Helper\FlashMessenger')
                    ->addErrorMessage('more-vehicles-than-total-auth-error');

                return $this->reload();
            }
        }
    }

    /**
     * Get the total vehicle authorisations
     *
     * @return int
     */
    protected function getTotalNumberOfAuthorisedVehicles()
    {
        if (empty($this->totalAuthorisedVehicles)) {
            $this->totalAuthorisedVehicles = $this->getLvaEntityService()->getTotalAuthorisation($this->params('id'));
        }

        return $this->totalAuthorisedVehicles;
    }

    /**
     * Get total number of vehicles
     *
     * @return int
     */
    protected function getTotalNumberOfVehicles()
    {
        if (empty($this->totalVehicles)) {
            $this->totalVehicles = $this->getServiceLocator()->get('Entity\Licence')
                ->getVehiclesTotal($this->getLicenceId());
        }

        return $this->totalVehicles;
    }

    /**
     * Check if the licence vehicle has a pending active disc
     *
     * @param int $id
     * @return boolean
     */
    protected function isDiscPendingForLicenceVehicle($id)
    {
        $ids = (array)$id;

        foreach ($ids as $id) {
            $results = $this->getServiceLocator()->get('Entity\LicenceVehicle')->getDiscPendingData($id);

            if ($this->isDiscPending($results)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Save the vehicle
     *
     * @param array $data
     * @param string $mode
     */
    protected function saveVehicle($data, $mode)
    {
        if ($mode !== 'add') {
            // We don't want these updating
            unset($data['licence-vehicle']['specifiedDate']);
            unset($data['licence-vehicle']['deletedDate']);
            unset($data['licence-vehicle']['discNo']);
            unset($data['vrm']);
        }

        $data = $this->alterDataForLva($data);

        $licenceVehicle = $data['licence-vehicle'];
        unset($data['licence-vehicle']);

        if (isset($licenceVehicle['receivedDate'])) {
            if (checkdate(
                (int)$licenceVehicle['receivedDate']['month'],
                (int)$licenceVehicle['receivedDate']['day'],
                (int)$licenceVehicle['receivedDate']['year']
            )) {
                $licenceVehicle['receivedDate'] = sprintf(
                    '%s-%s-%s',
                    $licenceVehicle['receivedDate']['year'],
                    $licenceVehicle['receivedDate']['month'],
                    $licenceVehicle['receivedDate']['day']
                );
            } else {
                unset($licenceVehicle['receivedDate']);
            }
        }

        $data['id'] = $this->params('child_id');

        $saved = $this->getServiceLocator()->get('Entity\Vehicle')->save($data);

        if ($mode == 'add') {
            $licenceVehicle['vehicle'] = $saved['id'];
            $licenceVehicle['licence'] = $this->getLicenceId();
        } else {
            $licenceVehicle['vehicle'] = $data['id'];
        }

        $saved = $this->getServiceLocator()->get('Entity\LicenceVehicle')->save($licenceVehicle);

        if (isset($licenceVehicle['id'])) {
            return $licenceVehicle['id'];
        }

        return $saved['id'];
    }

    /**
     * Generic form alterations
     *
     * @param \Zend\Form\Form $form
     * @param string $mode
     * @return \Zend\Form\Form
     */
    protected function alterVehicleForm($form, $mode)
    {
        $this->alterVehicleFormForLocation($form, $mode);

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $dataFieldset = $form->get('licence-vehicle');

        $formHelper->disableDateElement($dataFieldset->get('specifiedDate'));
        $formHelper->disableDateElement($dataFieldset->get('removalDate'));

        $dataFieldset->get('discNo')->setAttribute('disabled', 'disabled');

        // disable the vrm field on edit
        if ($mode === 'edit') {
            $formHelper->disableElement($form, 'data->vrm');
        }

        // Attach a validator to check the VRM doesn't already exist
        // We only really need to do this when posting
        if ($mode === 'add' && $this->getRequest()->isPost()) {

            $filter = $form->getInputFilter();
            $validators = $filter->get('data')->get('vrm')->getValidatorChain();

            $validator = new NewVrm();

            $validator->setType(ucwords($this->lva));
            $validator->setVrms($this->getVrmsForCurrentLicence());

            $validators->attach($validator);
        }

        return $form;
    }

    /**
     * Get vrms linked to licence
     *
     * @return array
     */
    protected function getVrmsForCurrentLicence()
    {
        return $this->getServiceLocator()->get('Entity\Licence')->getCurrentVrms($this->getLicenceId());
    }

    /**
     * If vehicle exists on another licence, add a message and confirmation field to the form
     *
     * @param array $data
     * @param \Zend\Form\Form $form
     * @return boolean
     */
    protected function checkIfVehicleExistsOnOtherLicences($data, $form)
    {
        $licences = $this->getOthersLicencesFromVrm($data['data']['vrm'], $this->getLicenceId());

        if (!empty($licences)) {

            $confirm = new Checkbox('confirm-add', array('label' => 'vehicle-belongs-to-another-licence-confirmation'));

            $confirm->setMessages(array($this->getErrorMessageForVehicleBelongingToOtherLicences($licences)));

            $form->get('licence-vehicle')->add($confirm);

            return true;
        }

        return false;
    }

    /**
     * Get a list of licences that have this vehicle (Except the current licence)
     *
     * @param string $vrm
     * @param int $licenceId
     */
    protected function getOthersLicencesFromVrm($vrm, $licenceId)
    {
        $licenceVehicles = $this->getServiceLocator()->get('Entity\Vehicle')->getLicencesForVrm($vrm);

        $licences = array();

        foreach ($licenceVehicles as $licenceVehicle) {
            if (isset($licenceVehicle['licence']['id'])
                && $licenceVehicle['licence']['id'] != $licenceId) {

                $licenceNumber = 'UNKNOWN';

                if (empty($licenceVehicle['licence']['licNo'])
                    && isset($licenceVehicle['licence']['applications'][0])
                ) {
                    $licenceNumber = 'APP-' . $licenceVehicle['licence']['applications'][0]['id'];
                } else {
                    $licenceNumber = $licenceVehicle['licence']['licNo'];
                }

                $licences[] = $licenceNumber;
            }
        }

        return $licences;
    }

    /**
     * We need to manually translate the message, as we need to optionally display a licence number
     * Based on whether we are internal or external
     *
     * @param array $licences
     * @return string
     */
    protected function getErrorMessageForVehicleBelongingToOtherLicences($licences)
    {
        $translator = $this->getServiceLocator()->get('Helper\Translation');

        $translationKey = 'vehicle-belongs-to-another-licence-message-' . $this->lva;

        // Internally we can add the licence numbers
        if ($this->location == 'internal') {

            if (count($licences) > 1) {
                $translationKey .= '-multiple';
            }

            return sprintf($translator->translate($translationKey), implode(', ', $licences));
        }

        return $translator->translate($translationKey);
    }

    /**
     * No-op, overridden in different sections
     *
     * @param array $data
     * @return array
     */
    protected function alterDataForLva($data)
    {
        return $data;
    }
}

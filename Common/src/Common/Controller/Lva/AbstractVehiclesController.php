<?php

/**
 * Shared logic for Goods *AND* PSV controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva;

use Zend\Form\Element\Checkbox;
use Common\Controller\Lva\Interfaces\AdapterAwareInterface;
use Common\Service\Entity\LicenceEntityService;

/**
 * Shared logic for Goods *AND* PSV controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractVehiclesController extends AbstractController implements AdapterAwareInterface
{
    use Traits\AdapterAwareTrait,
        Traits\CrudTableTrait;

    protected $totalAuthorisedVehicles = array();
    protected $totalVehicles = array();

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
     * Get the total vehicle authorisations
     *
     * @return int
     */
    abstract protected function getTotalNumberOfAuthorisedVehicles();

    /**
     * Get total number of vehicles
     *
     * @return int
     */
    abstract protected function getTotalNumberOfVehicles();

    /**
     * Hijack the crud action check so we can validate the add button
     *
     * @param string $action
     */
    protected function checkForAlternativeCrudAction($action)
    {
        if ($action === 'reprint') {
            $post = (array)$this->getRequest()->getPost();

            $id = $post['table']['id'];

            if ($this->isDiscPendingForLicenceVehicle($id)) {

                $this->getServiceLocator()->get('Helper\FlashMessenger')
                    ->addErrorMessage('reprint-pending-disc-error');

                return $this->reload();
            }
        }

        if ($action === 'add') {
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
     * Check if the licence vehicle has a pending active disc
     *
     * @param int $id
     * @return boolean
     */
    protected function isDiscPendingForLicenceVehicle($id)
    {
//        $ids = (array)$id;
//
//        foreach ($ids as $id) {
//            $results = $this->getServiceLocator()->get('Entity\LicenceVehicle')->getDiscPendingData($id);
//
//            if ($this->isDiscPending($results)) {
//                return true;
//            }
//        }
//
//        return false;
    }

    protected function preSaveVehicle($data, $mode)
    {
        return $data;
    }

    protected function postSaveVehicle($licenceVehicleId, $mode)
    {
        // No-op
    }

    /**
     * Save the vehicle
     *
     * @param array $data
     * @param string $mode
     */
    protected function saveVehicle($data, $mode)
    {
        $data = $this->preSaveVehicle($data, $mode);

        if ($mode !== 'add') {
            // We don't want these updating
            $data = $this->getAdapter()->maybeUnsetSpecifiedDate($data);
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

        $licenceVehicle = $this->getAdapter()->maybeFormatRemovedAndSpecifiedDates($licenceVehicle);

        // persist the vehicle first...
        $saved = $this->getServiceLocator()->get('Entity\Vehicle')->save($data);

        // then if this is a new record, store it ID against the licence vehicle
        if ($mode === 'add') {
            $licenceVehicle['vehicle'] = $saved['id'];
            $licenceVehicle['licence'] = $this->getLicenceId();
        } else {
            $licenceVehicle['vehicle'] = $data['id'];
        }

        if (in_array($this->lva, ['application', 'variation'])) {
            $licenceVehicle['application'] = $this->getIdentifier();
        }

        $saved = $this->getServiceLocator()->get('Entity\LicenceVehicle')->save($licenceVehicle);

        if (isset($saved['id'])) {
            $licenceVehicleId = $saved['id'];
        } elseif (!empty($licenceVehicle['id'])) {
            $licenceVehicleId = $licenceVehicle['id'];
        }

        $this->postSaveVehicle($licenceVehicleId, $mode);

        return $licenceVehicleId;
    }

    protected function canAddAnother()
    {
        $totalAuth = $this->getTotalNumberOfAuthorisedVehicles();
        $totalVehicles = $this->getTotalNumberOfVehicles();

        return $totalVehicles < ($totalAuth - 1);
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

        $translationKey = 'vehicle-belongs-to-another-licence-message-' . $this->location;

        // Internally we can add the licence numbers
        if ($this->location === 'internal') {

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

    /**
     * Check if the disc is pending
     *
     * @param array $licenceVehicleData
     * @return boolean
     */
    protected function isDiscPending($licenceVehicleData)
    {
        if (empty($licenceVehicleData['specifiedDate']) && empty($licenceVehicleData['removalDate'])) {
            return true;
        }

        if (isset($licenceVehicleData['goodsDiscs']) && !empty($licenceVehicleData['goodsDiscs'])) {
            $currentDisc = $licenceVehicleData['goodsDiscs'][0];

            if (empty($currentDisc['ceasedDate']) && empty($currentDisc['discNo'])) {

                return true;
            }
        }

        return false;
    }

    /**
     * Set appropriate default values on vehicle date fields
     *
     * @param Form $form
     * @param DateTime $currentDate
     * @return Form
     */
    protected function setDefaultDates($form, $currentDate)
    {
        $fieldset = $form->get('licence-vehicle');

        // receivedDate gets removed in some contexts
        if ($fieldset->has('receivedDate')) {
            // default 'Received date' to the current date if it is not set
            $receivedDate = $fieldset->get('receivedDate')->getValue();
            $receivedDate = trim($receivedDate, '-'); // date element returns '--' when empty!
            if (empty($receivedDate)) {
                $fieldset->get('receivedDate')->setValue($currentDate);
            }
        }
        return $form;
    }

    /**
     * Get the delete message.
     *
     * @return string
     */
    public function getDeleteMessage()
    {
        $toDelete = count(explode(',', $this->params('child_id')));
        $total = $this->getTotalNumberOfVehicles();

        $licence = $this->getServiceLocator()->get('Entity\Licence')->getOverview($this->getLicenceId());

        $acceptedLicenceTypes = array(
            LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
            LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
        );

        if (!in_array($licence['licenceType']['id'], $acceptedLicenceTypes)) {
            return 'delete.confirmation.text';
        }

        if ($total !== $toDelete) {
            return 'delete.confirmation.text';
        }

        return 'deleting.all.vehicles.message';
    }

    /**
     * Transfer vehicles action
     */
    public function transferAction()
    {
        return $this->transferVehicles();
    }

    /**
     * Transfer vehicles
     */
    protected function transferVehicles()
    {
        $form = $this->getVehicleTransferForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData((array) $request->getPost());
            if ($form->isValid()) {
                $response = $this->getServiceLocator()->get('BusinessServiceManager')
                    ->get('Lva\TransferVehicles')
                    ->process(
                        [
                            'data' => $form->getData(),
                            'sourceLicenceId' => $this->getLicenceId(),
                            'targetLicenceId' => $form->get('data')->get('licence')->getValue(),
                            'id' => $this->params()->fromRoute('child_id')
                        ]
                    );

                if ($response->isOk()) {
                    $this->getServiceLocator()
                        ->get('Helper\FlashMessenger')
                        ->addSuccessMessage('licence.vehicles_transfer.form.vehicles_transfered');
                    return $this->redirect()->toRouteAjax(
                        null,
                        array($this->getIdentifierIndex() => $this->getIdentifier())
                    );
                }
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage($response->getMessage());
            }
        }
        return $this->renderForm($form);
    }

    /**
     * Get vehicles transfer form
     * 
     * @return Common\Form\Form
     */
    protected function getVehicleTransferForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $form = $formHelper->createForm('Lva\VehiclesTransfer');
        $licences = $this->getServiceLocator()
            ->get('Entity\Licence')
            ->getOtherActiveLicences($this->params()->fromRoute('licence'));
        $form->get('data')->get('licence')->setValueOptions($licences);
        $formHelper->setFormActionFromRequest($form, $this->getRequest());
        return $form;
    }

    /**
     * Alter table. No-op but is extended in certain sections
     */
    protected function alterTable($table)
    {
        return $table;
    }
}

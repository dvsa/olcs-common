<?php

/**
 * Vehicles Declarations Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Service\Entity\LicenceEntityService;

/**
 * Vehicles Declarations Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractVehiclesDeclarationsController extends AbstractController
{
    /**
     * Action data map
     *
     * @var array
     */
    protected $dataMap = array(
        'main' => array(
            'mapFrom' => array(
                'application',
                'smallVehiclesIntention',
                'nineOrMore',
                'mainOccupation',
                'limousinesNoveltyVehicles'
            )
        )
    );

    protected $data;

    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->getFormData();
        }

        $form = $this->getForm();

        $this->alterForm($form);

        $this->getServiceLocator()->get('Script')->loadFile('vehicle-declarations');

        if ($request->isPost() && $form->isValid()) {

            $this->save($data);

            $this->postSave('vehicles_declarations');

            return $this->completeSection('vehicles_declarations');
        }

        return $this->render('vehicles_declarations', $form);
    }

    protected function getForm()
    {
        $form = $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\VehiclesDeclarations');

        return $form;
    }

    protected function getFormData()
    {
        return $this->formatDataForForm($this->loadData());
    }

    protected function loadData()
    {
        if ($this->data === null) {
            $this->data = $this->getServiceLocator()->get('Entity\Application')
                ->getDataForVehiclesDeclarations($this->getApplicationId());
        }

        return $this->data;
    }

    /**
     * Format data for dorm
     *
     * @param arary $data
     * @return array
     */
    protected function formatDataForForm($data)
    {
        return array(
            'version' => $data['version'],
            'smallVehiclesIntention' => array(
                'psvOperateSmallVhl' => $data['psvOperateSmallVhl'],
                'psvSmallVhlNotes' => $data['psvSmallVhlNotes'],
                'psvSmallVhlConfirmation' => $data['psvSmallVhlConfirmation']
            ),
            'nineOrMore' => array(
                'psvNoSmallVhlConfirmation' => $data['psvNoSmallVhlConfirmation']
            ),
            'mainOccupation' => array(
                'psvMediumVhlConfirmation' => $data['psvMediumVhlConfirmation'],
                'psvMediumVhlNotes' => $data['psvMediumVhlNotes']
            ),
            'limousinesNoveltyVehicles' => array(
                'psvLimousines' => $data['psvLimousines'],
                'psvNoLimousineConfirmation' => $data['psvNoLimousineConfirmation'],
                'psvOnlyLimousinesConfirmation' => $data['psvOnlyLimousinesConfirmation']
            )
        );
    }

    /**
     * Add customisation to the form dependent on which of five scenarios
     * is in play for OLCS-2855
     *
     * @param Zend\Form\Form $form
     * @return Zend\Form\Form
     */
    protected function alterForm($form)
    {
        $this->alterFormForLva($form);

        // We always need to load data, even if we have posted, so we know how to alter the form
        $data = $this->loadData();

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        // If this traffic area has no Scottish Rules flag, set it to false.
        if (!isset($data['licence']['trafficArea']['isScotland'])) {
            $data['licence']['trafficArea']['isScotland'] = false;
        }

        // In some cases, totAuthSmallVhl etc. can be set NULL, and we
        // need to evaluate as zero, so fix that here.
        $arrayCheck = array('totAuthSmallVehicles', 'totAuthMediumVehicles', 'totAuthLargeVehicles');

        foreach ($arrayCheck as $attribute) {
            if (is_null($data[$attribute])) {
                $data[$attribute] = 0;
            }
        }

        // @see https://jira.i-env.net/browse/OLCS-2853
        if ($data['licenceType']['id'] !== LicenceEntityService::LICENCE_TYPE_RESTRICTED
            || $data['totAuthMediumVehicles'] === 0
        ) {
            $formHelper->remove($form, 'mainOccupation');
        }

        if ($data['totAuthSmallVehicles'] === 0) {
            $formHelper->remove($form, 'smallVehiclesIntention');
            return;
        }

        $formHelper->remove($form, 'nineOrMore');

        if ($data['totAuthMediumVehicles'] === 0 && $data['totAuthLargeVehicles'] === 0) {
            $formHelper->remove($form, 'limousinesNoveltyVehicles->psvOnlyLimousinesConfirmationLabel');
            $formHelper->remove($form, 'limousinesNoveltyVehicles->psvOnlyLimousinesConfirmation');
        }

        if ($data['licence']['trafficArea']['isScotland']) {
            $formHelper->remove($form, 'smallVehiclesIntention->psvOperateSmallVhl');
            $formHelper->remove($form, 'smallVehiclesIntention->psvSmallVhlNotes');
        }
    }

    /**
     * Save the form data
     *
     * @param array $data
     * @param string $service
     */
    protected function save($data)
    {
        $saveData = $this->getServiceLocator()->get('Helper\Data')->processDataMap($data, $this->dataMap);

        $saveData['version'] = $data['version'];
        $saveData['id'] = $this->getApplicationId();

        $this->getServiceLocator()->get('Entity\Application')->save($saveData);
    }
}

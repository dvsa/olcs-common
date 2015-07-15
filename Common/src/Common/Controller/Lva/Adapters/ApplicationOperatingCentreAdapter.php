<?php

/**
 * Application Operating Centre Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Zend\Form\Form;

use Dvsa\Olcs\Transfer\Query\Application\Schedule41;

/**
 * Application Operating Centre Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationOperatingCentreAdapter extends AbstractOperatingCentreAdapter
{
    protected $lva = 'application';

    protected $entityService = 'Entity\ApplicationOperatingCentre';

    /**
     * Extend the delete behaviour to check traffic area
     */
    public function delete()
    {
        parent::delete();

        $this->checkTrafficArea();
    }

    /**
     * Check Traffic Area After Crud Action
     *
     * @param array $data
     */
    public function checkTrafficAreaAfterCrudAction($data)
    {
        if (is_array($data['action'])) {
            // in this scenario we can safely assume the action is 'edit',
            // in which case we can bail out nice and early
            return;
        }

        $action = strtolower($data['action']);

        if ($action === 'add' && !$this->getTrafficArea()) {

            $data = (array)$this->getController()->getRequest()->getPost();

            $trafficArea = isset($data['dataTrafficArea']['trafficArea'])
                ? $data['dataTrafficArea']['trafficArea']
                : '';

            if (empty($trafficArea) && $this->getOperatingCentresCount()) {
                $this->getServiceLocator()
                    ->get('Helper\FlashMessenger')
                    ->addWarningMessage('select-traffic-area-error');

                return $this->getController()->redirect()->toRoute(null, array(), array(), true);
            }
        }
    }

    /**
     * Check traffic area (We call this after deleting an OC)
     *
     * Resets the traffic area AND enforcement area on the licence
     */
    protected function checkTrafficArea()
    {
        if ($this->getOperatingCentresCount() === 0) {
            $licenceId = $this->getLicenceAdapter()->getIdentifier();
            $this->getServiceLocator()
                ->get('Entity\Licence')
                ->setEnforcementArea($licenceId, null)
                ->setTrafficArea($licenceId, null);
        }
    }

    /**
     * Format data for form
     *
     * @param array $data
     * @param array $tableData
     * @param array $licenceData
     * @return array
     */
    protected function formatDataForForm(array $data, array $tableData, array $licenceData)
    {
        $formData = parent::formatDataForForm($data, $tableData, $licenceData);

        if (isset($data['licence']['enforcementArea']['id'])) {
            $formData['dataTrafficArea']['enforcementArea'] = $data['licence']['enforcementArea']['id'];
        }

        return $formData;
    }

    public function alterForm(Form $form)
    {
        $form = parent::alterForm($form);

        $response = $this->getController()
            ->handleQuery(
                Schedule41::create(
                    [
                        'id' => $this->getApplicationAdapter()->getIdentifier()
                    ]
                )
            );

        if ($response->isOk()) {
            $result = $response->getResult();

            if ($result['goodsOrPsv']['id'] !== 'lcat_gv') {
                $form->get('table')->get('table')->getTable()->removeAction('schedule41');
            }

            if ($result['status']['id'] !== 'apsts_consideration' || $result['hasS4Records'] == true) {
                $form->get('table')->get('table')->getTable()->removeAction('schedule41');
            }
        }

        return $form;
    }
}

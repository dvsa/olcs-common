<?php

/**
 * Authorisation Controller
 *
 * External - Application - Authorisation Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Controller\Application\OperatingCentres;

use Common\Controller\Traits;

/**
 * Authorisation Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class AuthorisationController extends OperatingCentresController
{
    use Traits\GenericIndexAction,
        Traits\GenericAddAction,
        Traits\GenericEditAction;

    protected $sectionServiceName = 'OperatingCentre\\ExternalApplicationAuthorisation';

    /**
     * Delete sub action
     *
     * @return Response
     */
    public function deleteAction()
    {
        if ($this->getSectionService()->getOperatingCentresCount() === 1
            && $this->getActionId()
        ) {
            $this->getSectionService('TrafficArea')->setTrafficArea(null);
        }

        return $this->delete();
    }

    /**
     * Get the action data bundle
     *
     * @return array
     */
    protected function getActionDataBundle()
    {
        return $this->getSectionService()->getActionDataBundle();
    }

    /**
     * Get the action data map
     *
     * @return array
     */
    protected function getActionDataMap()
    {
        return $this->getSectionService()->getActionDataMap();
    }

    /**
     * Get action service
     *
     * @return string
     */
    protected function getActionService()
    {
        return $this->getSectionService()->getActionService();
    }

    /**
     * Get data map
     *
     * @return array
     */
    protected function getDataMap()
    {
        return $this->getSectionService()->getDataMap();
    }

    /**
     * Get form tables
     *
     * @return array
     */
    protected function getFormTables()
    {
        return $this->getSectionService()->getFormTables();
    }

    /**
     * Get data bundle
     *
     * @return array
     */
    protected function getDataBundle()
    {
        return $this->getSectionService()->getDataBundle();
    }

    /**
     * Get service
     *
     * @return type
     */
    protected function getService()
    {
        return $this->getSectionService()->getService();
    }

    /**
     * Get data for table
     *
     * @param string $id
     */
    protected function getFormTableData($id, $table)
    {
        return $this->getSectionService()->getFormTableData($id);
    }

    /**
     * Alter action form
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function alterActionForm($form)
    {
        return $this->getSectionService()->alterActionForm($form);
    }

    /**
     * Alter the section form
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function alterForm($form)
    {
        return $this->getSectionService()->alterForm($form);
    }

    /**
     * Save method
     *
     * @param array $data
     * @param string $service
     */
    protected function save($data, $service = null)
    {
        return $this->getSectionService()->save($data, $service);
    }

    /**
     * Process save crud
     *
     * @param array $data
     */
    protected function processSaveCrud($data)
    {
        if ($this->getSectionService()->setTrafficAreaAfterCrudAction($data) === false) {

            $this->addWarningMessage('select-traffic-area-error');
            $this->setCaughtResponse($this->redirect()->toRoute(null, array(), array(), true));
            return;
        }

        return parent::processSaveCrud($data);
    }

    /**
     * Process the action load data
     *
     * @param array $oldData
     */
    protected function processActionLoad($oldData)
    {
        return $this->getSectionService()->processActionLoad($oldData);
    }
}

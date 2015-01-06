<?php

/**
 * Common variation OC controller logic
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use Zend\Form\Form;

/**
 * Common variation OC controller logic
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait VariationOperatingCentresControllerTrait
{
    /**
     * Get the entity name representing this LVA's Operating Centres
     */
    protected function getLvaOperatingCentreEntity()
    {
        return 'Entity\\ApplicationOperatingCentre';
    }

    /**
     * Generic delete functionality; usually does the trick but
     * can be overridden if not
     */
    public function deleteAction()
    {
        if ($this->canDeleteRecord($this->params('child_id'))) {
            return parent::deleteAction();
        }

        // JS should restrict requests to only valid ones, however we better double check
        return $this->processUndeletableResponse();
    }

    public function restoreAction()
    {
        $ref = $this->params('child_id');

        list($type, $id) = $this->splitTypeAndId($ref);

        if ($type === 'A') {
            $this->getServiceLocator()->get('Entity\ApplicationOperatingCentre')
                ->delete($id);

            return $this->redirect()->toRouteAjax(null, array('action' => null, 'child_id' => null), array(), true);
        }

        // @todo restore updated version
    }

    protected function processUndeletableResponse()
    {
        $this->getServiceLocator()->get('Helper\FlashMessenger')
                ->addErrorMessage('could-not-remove-message');

        return $this->redirect()->toRouteAjax(null, array('child_id' => null), array(), true);
    }

    /**
     * This is more complicated than I would like, but we could have either an application oc record "A123" or a licence
     * operating centre record "L123", so we need to cater for both
     *
     * @return mixed
     */
    protected function delete()
    {
        $ref = $this->params('child_id');

        // JS should restrict requests to only valid ones, however we better double check
        if (!$this->canDeleteRecord($ref)) {
            return $this->processUndeletableResponse();
        }

        list($type, $id) = $this->splitTypeAndId($ref);

        if ($type === 'A') {
            $this->getServiceLocator()->get('Entity\ApplicationOperatingCentre')->delete($id);
            return;
        } else {
            $this->getServiceLocator()->get('Entity\LicenceOperatingCentre')
                ->variationDelete($id, $this->getApplicationId());
        }
    }

    protected function splitTypeAndId($ref)
    {
        $type = substr($ref, 0, 1);

        if (is_numeric($type)) {
            return array(null, $ref);
        }

        $id = (int)substr($ref, 1);

        return array($type, $id);
    }

    /**
     * Un-edited licence operating centre records and updated version stored in application operating centre
     * can be deleted
     *
     * @param type $ref
     */
    protected function canDeleteRecord($ref)
    {
        list($type, $id) = $this->splitTypeAndId($ref);

        $aocDataService = $this->getServiceLocator()->get('Entity\ApplicationOperatingCentre');

        // If we have an application operating centre record
        if ($type === 'A') {
            $record = $aocDataService->getById($id);

            return in_array($record['action'], ['U', 'A']);
        }

        $locDataService = $this->getServiceLocator()->get('Entity\LicenceOperatingCentre');

        $record = $locDataService->getAddressData($id);

        $ocId = $record['operatingCentre']['id'];

        $aocRecord = $aocDataService->getByApplicationAndOperatingCentre($this->getApplicationId(), $ocId);

        return empty($aocRecord);
    }

    protected function getChildId()
    {
        $ref = $this->params('child_id');

        return $this->splitTypeAndId($ref)[1];
    }

    public function alterActionForm(Form $form)
    {
        $form = parent::alterActionForm($form);

        if ($this->location === 'external') {
            $formHelper = $this->getServiceLocator()->get('Helper\Form');
            $formHelper->disableElements($form->get('address'));
        }

        return $form;
    }
}

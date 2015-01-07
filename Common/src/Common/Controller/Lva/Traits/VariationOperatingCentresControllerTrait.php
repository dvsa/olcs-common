<?php

/**
 * Common variation OC controller logic
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

/**
 * Common variation OC controller logic
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait VariationOperatingCentresControllerTrait
{
    /**
     * Generic delete functionality; usually does the trick but
     * can be overridden if not
     */
    public function deleteAction()
    {
        if ($this->getAdapter()->canDeleteRecord($this->params('child_id'))) {
            return parent::deleteAction();
        }

        // JS should restrict requests to only valid ones, however we better double check
        return $this->getAdapter()->processUndeletableResponse();
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
}

<?php

/**
 * Application Operating Centre Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

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
     */
    protected function checkTrafficArea()
    {
        if ($this->getOperatingCentresCount() === 0) {
            $this->getServiceLocator()
                ->get('Entity\Licence')
                ->setTrafficArea(
                    $this->getLicenceAdapter()->getIdentifier(),
                    null
                );
        }
    }
}

<?php

/**
 * Application Operating Centres Controller Trait
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva\Traits;

/**
 * Application Operating Centres Controller Trait
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
trait ApplicationOperatingCentresControllerTrait
{
    /**
     * Check Traffic Area After Crud Action
     *
     * @param array $data
     */
    protected function checkTrafficAreaAfterCrudAction($data)
    {
        if (is_array($data['action'])) {
            // in this scenario we can safely assume the action is 'edit',
            // in which case we can bail out nice and early
            return;
        }

        $action = strtolower($data['action']);

        $data = (array)$this->getRequest()->getPost();

        if ($action === 'add' && !$this->getTrafficArea()) {
            $trafficArea = isset($data['dataTrafficArea']['trafficArea'])
                ? $data['dataTrafficArea']['trafficArea']
                : '';

            if (empty($trafficArea) && $this->getOperatingCentresCount()) {
                $this->getServiceLocator()
                    ->get('Helper\FlashMessenger')
                    ->addWarningMessage('select-traffic-area-error');

                return $this->redirect()->toRoute(null, array(), array(), true);
            }
        }
    }

    protected function checkTrafficAreaAfterDelete()
    {
        if ($this->getOperatingCentresCount() === 0) {
            $this->getServiceLocator()
                ->get('Entity\Licence')
                ->setTrafficArea(
                    $this->getLicenceId(),
                    null
                );
        }
    }
}

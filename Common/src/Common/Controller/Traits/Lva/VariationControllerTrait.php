<?php

/**
 * Variation Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\Lva;

/**
 * Variation Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait VariationControllerTrait
{
    /**
     * Hook into the dispatch before the controller action is executed
     */
    protected function preDispatch()
    {
        $applicationId = $this->getApplicationId();

        if (!$this->isApplicationVariation($applicationId)) {
            return $this->notFoundAction();
        }

        return $this->checkForRedirect($applicationId);
    }
}

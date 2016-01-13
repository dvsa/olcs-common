<?php

/**
 * Common Application Controller Trait Stub
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Traits\Stubs;

use Common\Controller\Lva\Traits\CommonApplicationControllerTrait;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Common Application Controller Trait Stub
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommonApplicationControllerTraitStub extends AbstractActionController
{
    use CommonApplicationControllerTrait;

    protected $lva = 'application';

    public function callPreDispatch()
    {
        return $this->preDispatch();
    }

    public function callGetCompletionStatuses($applicationId)
    {
        return $this->getCompletionStatuses($applicationId);
    }

    public function callUpdateCompletionStatuses($applicationId, $section)
    {
        return $this->updateCompletionStatuses($applicationId, $section);
    }

    public function callIsApplicationNew($applicationId)
    {
        return $this->isApplicationNew($applicationId);
    }

    public function callIsApplicationVariation($applicationId)
    {
        return $this->isApplicationVariation($applicationId);
    }

    public function callGetApplicationType($applicationId)
    {
        return $this->getApplicationType($applicationId);
    }

    public function callGetApplicationId()
    {
        return $this->getApplicationId();
    }

    public function callGetLicenceId($applicationId)
    {
        return $this->getLicenceId($applicationId);
    }

    public function callCompleteSection($section)
    {
        return $this->completeSection($section);
    }

    public function callPostSave($section)
    {
        return $this->postSave($section);
    }

    public function callGoToNextSection($section)
    {
        return $this->goToNextSection($section);
    }

    public function notFoundAction()
    {

    }

    public function checkForRedirect($lvaId)
    {

    }
}

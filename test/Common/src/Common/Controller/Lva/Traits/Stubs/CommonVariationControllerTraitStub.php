<?php

/**
 * Common Variation Controller Trait Stub
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Traits\Stubs;

use Common\Controller\Lva\Traits\CommonVariationControllerTrait;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Common Variation Controller Trait Stub
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommonVariationControllerTraitStub extends AbstractActionController
{
    use CommonVariationControllerTrait;

    protected $lva = 'variation';

    public function callPreDispatch()
    {
        return $this->preDispatch();
    }

    public function callPostSave($section)
    {
        return $this->postSave($section);
    }

    public function callGoToNextSection($currentSection)
    {
        return $this->goToNextSection($currentSection);
    }
}

<?php

/**
 * Enabled Section Trait Stub
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Controller\Lva\Traits\Stubs;

use Common\Controller\Lva\Traits\EnabledSectionTrait;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Enabled Section Trait Stub
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class EnabledSectionTraitStub extends AbstractActionController
{
    use EnabledSectionTrait;

    public function callSetEnabledAndCompleteFlagOnSections($accessibleSections, $applicationCompletion)
    {
        return $this->setEnabledAndCompleteFlagOnSections($accessibleSections, $applicationCompletion);
    }
}

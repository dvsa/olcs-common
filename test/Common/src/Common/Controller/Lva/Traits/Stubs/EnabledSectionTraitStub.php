<?php

namespace CommonTest\Common\Controller\Lva\Traits\Stubs;

use Common\Controller\Lva\Traits\EnabledSectionTrait;
use Common\Service\Helper\RestrictionHelperService;
use Common\Service\Helper\StringHelperService;
use Laminas\Mvc\Controller\AbstractActionController;

/**
 * Enabled Section Trait Stub
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class EnabledSectionTraitStub extends AbstractActionController
{
    use EnabledSectionTrait;

    public function __construct(RestrictionHelperService $restrictionHelper, StringHelperService $stringHelper)
    {
        $this->restrictionHelper = $restrictionHelper;
        $this->stringHelper = $stringHelper;
    }

    public function callSetEnabledAndCompleteFlagOnSections($accessibleSections, $applicationCompletion)
    {
        return $this->setEnabledAndCompleteFlagOnSections($accessibleSections, $applicationCompletion);
    }
}

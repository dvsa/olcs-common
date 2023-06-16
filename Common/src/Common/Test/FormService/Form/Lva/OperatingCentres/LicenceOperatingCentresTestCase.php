<?php

declare(strict_types=1);

namespace Common\Test\FormService\Form\Lva\OperatingCentres;

use Common\FormService\Form\Lva\Licence as LvaLicenceFormService;
use Common\Service\Helper\FormHelperService;
use ZfcRbac\Service\AuthorizationService;
use Mockery as m;

abstract class LicenceOperatingCentresTestCase extends OperatingCentresTestCase
{
    protected function setUpDefaultServices()
    {
        parent::setUpDefaultServices();
    }
}

<?php

namespace CommonTest\Controller\Lva\Traits\Stubs;

use Common\Controller\Lva\Traits\LicenceOperatingCentresControllerTrait;
use Zend\Mvc\Controller\AbstractActionController;

class LicenceOperatingCentresControllerTraitStub extends AbstractActionController
{
    use LicenceOperatingCentresControllerTrait;

    public function callDisableConditionalValidation($form)
    {
        return $this->disableConditionalValidation($form);
    }

    public function callFormatCrudDataForSave($data)
    {
        return $this->formatCrudDataForSave($data);
    }
}

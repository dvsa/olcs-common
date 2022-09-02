<?php

declare(strict_types=1);

namespace Common\Test\FormService\Form\Lva\OperatingCentres;

use Common\FormService\Form\Lva\Licence as LvaLicenceFormService;

abstract class LicenceOperatingCentresTestCase extends OperatingCentresTestCase
{
    protected function setUpDefaultServices()
    {
        parent::setUpDefaultServices();
        $this->lvaLicenceFormService();
    }

    /**
     * @return LvaLicenceFormService
     */
    protected function lvaLicenceFormService(): LvaLicenceFormService
    {
        if (!$this->formServiceManager()->has('lva-licence')) {
            $instance = new LvaLicenceFormService();
            $this->formServiceManager()->setService('lva-licence', $instance);
        }
        return $this->formServiceManager()->get('lva-licence');
    }
}

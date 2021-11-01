<?php

declare(strict_types=1);

namespace Common\Test\FormService\Form\Lva\OperatingCentres;

use Common\FormService\Form\Lva\Licence as LvaLicenceFormService;
use Laminas\View\Renderer\PhpRenderer;

abstract class LicenceOperatingCentresTestCase extends OperatingCentresTestCase
{
    protected function setUpDefaultServices()
    {
        parent::setUpDefaultServices();
        $this->viewRenderer();
        $this->lvaLicenceFormService();
    }

    /**
     * @return PhpRenderer
     */
    protected function viewRenderer(): PhpRenderer
    {
        if (!$this->serviceManager()->has('ViewRenderer')) {
            $instance = $this->setUpMockService(PhpRenderer::class);
            $this->serviceManager()->setService('ViewRenderer', $instance);
        }
        return $this->serviceManager()->get('ViewRenderer');
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

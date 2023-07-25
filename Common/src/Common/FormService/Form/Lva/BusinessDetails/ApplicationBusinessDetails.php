<?php

namespace Common\FormService\Form\Lva\BusinessDetails;

use Common\FormService\FormServiceInterface;
use Common\FormService\FormServiceManager;
use Common\Service\Helper\FormHelperService;
use ZfcRbac\Service\AuthorizationService;

/**
 * Application Business Details Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationBusinessDetails extends AbstractBusinessDetails
{
    protected FormServiceManager $formServiceLocator;
    protected FormHelperService $formHelper;

    public function __construct(FormHelperService $formHelper, FormServiceManager $formServiceLocator)
    {
        $this->formServiceLocator = $formServiceLocator;
        parent::__construct($formHelper);
    }
    protected function alterForm($form, $params)
    {
        $this->formServiceLocator->get('lva-application')->alterForm($form);
        parent::alterForm($form, $params);
    }
}

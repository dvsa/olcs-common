<?php

namespace Common\FormService\Form\Lva\BusinessDetails;

use Common\FormService\FormServiceManager;
use Common\Service\Helper\FormHelperService;

/**
 * Licence Business Details Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceBusinessDetails extends AbstractBusinessDetails
{
    protected FormServiceManager $formServiceLocator;
    protected FormHelperService $formHelper;

    public function __construct(FormHelperService $formHelper, FormServiceManager $formServiceLocator)
    {
        $this->formHelper = $formHelper;
        $this->formServiceLocator = $formServiceLocator;
    }

    protected function alterForm($form, $params)
    {
        $this->formServiceLocator->get('lva-licence')->alterForm($form);

        parent::alterForm($form, $params);
    }
}

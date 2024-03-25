<?php

namespace Common\FormService\Form\Lva\BusinessDetails;

use Common\FormService\FormServiceManager;
use Common\Service\Helper\FormHelperService;

/**
 * Variation Business Details Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationBusinessDetails extends AbstractBusinessDetails
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
        $this->formServiceLocator->get('lva-variation')->alterForm($form);

        parent::alterForm($form, $params);
    }
}

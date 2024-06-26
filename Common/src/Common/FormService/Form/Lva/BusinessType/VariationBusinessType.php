<?php

namespace Common\FormService\Form\Lva\BusinessType;

use Common\FormService\FormServiceManager;
use Common\Service\Helper\FormHelperService;
use Common\Service\Helper\GuidanceHelperService;
use Laminas\Form\Form;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Variation Business Type Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationBusinessType extends AbstractBusinessType
{
    protected $lva = 'variation';

    public function __construct(protected FormHelperService $formHelper, protected AuthorizationService $authService, protected GuidanceHelperService $guidanceHelper, protected FormServiceManager $formServiceLocator)
    {
    }

    /**
     * @return void
     */
    protected function alterForm(Form $form, $params)
    {
        $this->formServiceLocator->get('lva-variation')->alterForm($form);

        parent::alterForm($form, $params);
    }
}

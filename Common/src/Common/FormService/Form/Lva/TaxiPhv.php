<?php

namespace Common\FormService\Form\Lva;

use Common\Service\Helper\FormHelperService;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * TaxiPhv Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TaxiPhv extends AbstractLvaFormService
{
    protected FormHelperService $formHelper;

    protected AuthorizationService $authService;

    public function __construct(FormHelperService $formHelper, AuthorizationService $authService)
    {
        $this->formHelper = $formHelper;
        $this->authService = $authService;
    }

    public function getForm()
    {
        $form = $this->formHelper->createForm('Lva\TaxiPhv');

        $this->alterForm($form);

        return $form;
    }

    /**
     * Make form alterations
     *
     * @param \Laminas\Form\Form $form
     * @return \Laminas\Form\Form
     */
    protected function alterForm($form)
    {
        $this->removeFormAction($form, 'cancel');
        return $form;
    }
}

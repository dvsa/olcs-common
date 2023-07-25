<?php

namespace Common\FormService\Form\Lva\People;

use Common\Form\Form;
use Common\Service\Helper\FormHelperService;
use ZfcRbac\Service\AuthorizationService;

/**
 * Application People
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationPeople extends AbstractPeople
{
    protected $lva = 'application';

    protected FormHelperService $formHelper;
    protected AuthorizationService $authService;

    public function __construct(FormHelperService $formHelper, AuthorizationService $authService)
    {
        $this->formHelper = $formHelper;
        $this->authService = $authService;
    }

    /**
     * Alter form
     *
     * @param Form  $form   Form class
     * @param array $params Parameters for form
     *
     * @return Form
     */
    protected function alterForm(Form $form, array $params = [])
    {
        parent::alterForm($form, $params);

        $this->removeFormAction($form, 'cancel');
    }
}

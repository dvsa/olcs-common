<?php

namespace Common\FormService\Form\Lva;

use Common\Service\Helper\FormHelperService;
use ZfcRbac\Service\AuthorizationService;

/**
 * PSV Vehicles Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class PsvVehicles extends AbstractLvaFormService
{
    protected FormHelperService $formHelper;
    protected AuthorizationService $authService;

    public function __construct(FormHelperService $formHelper, AuthorizationService $authService)
    {
        $this->formHelper = $formHelper;
        $this->authService = $authService;
    }

    protected $showShareInfo = false;

    public function getForm()
    {
        $form = $this->formHelper->createForm('Lva\PsvVehicles');

        $this->alterForm($form);

        if ($this->showShareInfo === false) {
            $this->formHelper->remove($form, 'shareInfo');
        }

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
        return $form;
    }
}

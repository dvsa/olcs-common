<?php

namespace Common\FormService\Form\Lva;

use Common\Service\Helper\FormHelperService;

class VehiclesDeclarationsBoth
{
    public function __construct(protected FormHelperService $formHelper)
    {
    }

    public function getForm()
    {
        $form = $this->formHelper->createForm('Lva\VehiclesDeclarationsBoth');

        $this->alterForm($form);

        return $form;
    }

    protected function alterForm($form)
    {
        return $form;
    }
}

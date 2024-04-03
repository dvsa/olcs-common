<?php

namespace Common\FormService\Form\Lva;

use Common\Service\Helper\FormHelperService;

/**
 * Generic Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GenericVehiclesVehicle
{
    protected FormHelperService $formHelper;

    public function __construct(FormHelperService $formHelper)
    {
        $this->formHelper = $formHelper;
    }

    /**
     * Generic form alterations
     *
     * @param \Laminas\Form\Form $form
     * @param array $params
     */
    public function alterForm($form, $params): void
    {
        if ($params['mode'] === 'edit') {
            $this->formHelper->disableElement($form, 'data->vrm');
        }

        if ($params['mode'] === 'edit' || !$params['canAddAnother']) {
            $form->get('form-actions')->remove('addAnother');
        }
    }
}

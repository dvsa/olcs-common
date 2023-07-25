<?php

namespace Common\FormService\Form\Lva;

use Common\Service\Helper\FormHelperService;

/**
 * Common Vehicles Search Form
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommonVehiclesSearch
{
    protected FormHelperService $formHelper;

    public function __construct(FormHelperService $formHelper)
    {
        $this->formHelper = $formHelper;
    }
    /**
     * Get form
     *
     * @return \Laminas\Form\FormInterface
     */
    public function getForm()
    {
        return $this->formHelper->createForm('Lva\VehicleSearch', false);
    }
}

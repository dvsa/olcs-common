<?php

/**
 * Common Vehicles Search Form
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;

/**
 * Common Vehicles Search Form
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommonVehiclesSearch extends AbstractFormService
{
    public function getForm()
    {
        return $this->getFormHelper()->createForm('Lva\VehicleSearch', false);
    }
}

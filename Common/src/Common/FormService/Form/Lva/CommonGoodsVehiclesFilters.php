<?php

/**
 * Common Goods Vehicles Filters Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;

/**
 * Common Goods Vehicles Filters Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommonGoodsVehiclesFilters extends AbstractFormService
{
    public function getForm()
    {
        $form = $this->getFormHelper()->createForm('Lva\VehicleFilter', false);

        $a2z = range('A', 'Z');

        $vrmOptions = array_merge(['All' => 'All'], array_combine($a2z, $a2z));

        $form->get('vrm')->setValueOptions($vrmOptions);

        return $form;
    }
}

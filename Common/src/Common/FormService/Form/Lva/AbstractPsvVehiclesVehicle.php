<?php

/**
 * Abstract Psv Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;

/**
 * Abstract Psv Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractPsvVehiclesVehicle extends AbstractFormService
{
    public function getForm($request, $params)
    {
        $form = $this->getFormHelper()
            ->createFormWithRequest('Lva\PsvVehiclesVehicle', $request);

        $this->alterForm($form, $params);

        return $form;
    }

    /**
     * Generic form alterations
     *
     * @param \Zend\Form\Form $form
     * @param array $params
     * @return \Zend\Form\Form
     */
    protected function alterForm($form, $params)
    {
        $this->getFormServiceLocator()->get('lva-psv-vehicles-vehicle')->alterForm($form);

        if (!in_array($params['action'], ['small-add', 'small-edit'])) {
            $this->getFormHelper()->remove($form, 'data->isNovelty');
            $this->getFormHelper()->remove($form, 'data->makeModel');
        }

        $this->getFormHelper()->remove($form, 'licence-vehicle->discNo');

        $this->getFormServiceLocator()->get('lva-generic-vehicles-vehicle')->alterForm($form, $params);
    }
}

<?php

/**
 * Abstract Goods Vehicles Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Abstract Goods Vehicles Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractGoodsVehiclesVehicle extends AbstractFormService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $lva;

    public function getForm($request, $params)
    {
        $form = $this->getFormHelper()->createFormWithRequest('Lva\GoodsVehiclesVehicle', $request);

        $this->alterForm($form, $params);

        return $form;
    }

    protected function alterForm($form, $params)
    {
        // This service is defined differently internally and externally
        // any logic that is split across internal or external should be placed in the
        // appropriate service
        $this->getFormServiceLocator()->get('lva-goods-vehicles-vehicle')->alterForm($form, $params);

        if ($params['isRemoved']) {
            $this->getFormHelper()->disableElements($form);
            $this->getFormHelper()->remove($form, 'form-actions->submit');
            $form->get('form-actions')->get('cancel')->setAttribute('disabled', false);
        }

        $this->getFormServiceLocator()->get('lva-generic-vehicles-vehicle')->alterForm($form, $params);
    }
}

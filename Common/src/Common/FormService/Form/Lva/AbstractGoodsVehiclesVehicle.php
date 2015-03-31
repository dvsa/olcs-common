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
use Common\Form\Elements\Validators\NewVrm;

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

        // disable the vrm field on edit
        if ($params['mode'] === 'edit') {
            $this->getFormHelper()->disableElement($form, 'data->vrm');
        }

        if ($params['mode'] === 'edit' || !$params['canAddAnother']) {
            $form->get('form-actions')->remove('addAnother');
        }

        // Disable the form if the vehicle is removed
        if ($params['isRemoved']) {
            $this->getFormHelper()->disableElements($form);
            $this->getFormHelper()->remove($form, 'form-actions->submit');
            $form->get('form-actions')->get('cancel')->setAttribute('disabled', false);
        }

        // Attach a validator to check the VRM doesn't already exist
        // We only really need to do this when posting
        if ($params['mode'] === 'add' && $params['isPost']) {

            $filter = $form->getInputFilter();
            $validators = $filter->get('data')->get('vrm')->getValidatorChain();

            $validator = new NewVrm();

            $validator->setType(ucwords($this->lva));
            $validator->setVrms($params['currentVrms']);

            $validators->attach($validator);
        }
    }
}

<?php

/**
 * PSV & Goods Licences & Variations Controller Trait
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use Zend\Form\Form;

/**
 * PSV Licence Controller Trait
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
trait PsvGoodsLicenceVariationControllerTrait
{
    /**
     * Remove vehicle question for licences / variations
     *
     * @param Olcs\Common\Form\Form
     */
    protected function alterFormForLva(Form $form)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $formHelper->remove($form, 'data->hasEnteredReg');
        $formHelper->remove($form, 'data->notice');
        return $form;
    }
}

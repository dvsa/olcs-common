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
abstract class AbstractGoodsVehicles extends AbstractFormService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $showShareInfo = false;

    public function getForm($table)
    {
        $form = $this->getFormHelper()->createForm('Lva\GoodsVehicles');

        $this->getFormHelper()->populateFormTable($form->get('table'), $table);

        $this->alterForm($form);

        if ($this->showShareInfo === false) {
            $this->getFormHelper()->remove($form, 'shareInfo');
        }

        return $form;
    }
}

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

    public function getForm($table, $isCrudPressed)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $formHelper->createForm('Lva\GoodsVehicles');

        $formHelper->populateFormTable($form->get('table'), $table);

        $this->alterForm($form, $isCrudPressed);

        return $form;
    }

    protected function alterForm($form, $isCrudPressed)
    {
        $rows = [$form->get('table')->get('rows')->getValue()];

        $oneRowInTablesRequiredValidator = $this->getServiceLocator()->get('oneRowInTablesRequired');
        $oneRowInTablesRequiredValidator->setRows($rows);
        $oneRowInTablesRequiredValidator->setCrud($isCrudPressed);

        $form->getInputFilter()->get('data')->get('hasEnteredReg')
            ->getValidatorChain()->attach($oneRowInTablesRequiredValidator);
    }
}

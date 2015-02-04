<?php

/**
 * Abstract Conditions Undertakings Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Controller\Lva\Interfaces\AdapterAwareInterface;

/**
 * Abstract Conditions Undertakings Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractConditionsUndertakingsController extends AbstractController implements AdapterAwareInterface
{
    use Traits\AdapterAwareTrait;

    /**
     * Conditions Undertakings section
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $this->postSave('conditions_undertakings');

            return $this->completeSection('conditions_undertakings');
        }

        $form = $this->getForm();

        $this->alterFormForLva($form);

        return $this->render('conditions_undertakings', $form);
    }

    /**
     * Get conditions undertakings form
     *
     * @return \Zend\Form\Form
     */
    protected function getForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $formHelper->createForm('Lva\ConditionsUndertakings');

        $formHelper->populateFormTable($form->get('table'), $this->getTable());

        return $form;
    }

    /**
     * Grab the table object
     *
     * @return \Common\Service\Table\TableBuilder
     */
    protected function getTable()
    {
        $tableBuilder = $this->getServiceLocator()->get('Table');

        return $tableBuilder->prepareTable('lva-conditions-undertakings', $this->getTableData());
    }

    /**
     * Grab the table data
     *
     * @return array
     */
    protected function getTableData()
    {
        return $this->getAdapter()->getTableData($this->getIdentifier());
    }
}

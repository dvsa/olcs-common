<?php

/**
 * FinancialEvidence Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\FormService\Form\Lva;

/**
 * FinancialEvidence Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FinancialEvidence extends AbstractLvaFormService
{
    public function getForm($request)
    {
        $form = $this->getFormHelper()->createFormWithRequest('Lva\FinancialEvidence', $request);

        $this->alterForm($form);

        return $form;
    }

    /**
     * Make form alterations
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function alterForm($form)
    {
        return $form;
    }
}

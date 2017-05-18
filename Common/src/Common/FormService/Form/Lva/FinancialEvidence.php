<?php

namespace Common\FormService\Form\Lva;

/**
 * FinancialEvidence Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FinancialEvidence extends AbstractLvaFormService
{
    /**
     * Get Form
     *
     * @param \Zend\Http\Request $request Request
     *
     * @return \Zend\Form\Form
     */
    public function getForm(\Zend\Http\Request $request)
    {
        $form = $this->getFormHelper()->createFormWithRequest('Lva\FinancialEvidence', $request);

        $this->alterForm($form);

        return $form;
    }

    /**
     * Make form alterations
     *
     * @param \Zend\Form\Form $form Form
     *
     * @return \Zend\Form\Form
     */
    protected function alterForm($form)
    {
        $form->get('evidence')->get('uploadNowRadio')->setName('uploadNow');
        $form->get('evidence')->get('uploadLaterRadio')->setName('uploadNow');
        $form->get('evidence')->get('sendByPostRadio')->setName('uploadNow');
        $this->getFormHelper()->remove($form, 'evidence->uploadNow');
        return $form;
    }
}

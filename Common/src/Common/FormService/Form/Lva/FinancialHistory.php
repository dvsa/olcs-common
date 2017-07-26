<?php

namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;
use Common\Form\Form;
use Zend\Http\Request;

/**
 * FinancialHistory Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FinancialHistory extends AbstractFormService
{
    /**
     * Get form
     *
     * @param Request $request Request
     * @param array   $data    Data for form
     *
     * @return Form
     */
    public function getForm($request, array $data = [])
    {
        /** @var Form $form */
        $form = $this->getFormHelper()->createFormWithRequest('Lva\FinancialHistory', $request);

        $this->alterForm($form, $data);

        return $form;
    }

    /**
     * Make form alterations
     *
     * @param \Zend\Form\Form $form Form
     * @param array           $data Data for form
     *
     * @return \Zend\Form\Form
     */
    protected function alterForm(Form $form, array $data = [])
    {
        if (isset($data['lva']) && $data['lva'] === 'variation') {
            $this->updateInsolvencyConfirmationLabel($form, $data);
        }

        return $form;
    }

    /**
     * If the licence is NI then update the label.  Used in current controller
     * and CommonVariationControllerTrait.
     *
     * @param Form  $form Form
     * @param array $data Api/Form Data
     *
     * @return Form
     */
    protected function updateInsolvencyConfirmationLabel(Form $form, $data = null)
    {
        if (isset($data['niFlag']) && $data['niFlag'] === 'Y') {
            $dataFieldset = $form->get('data');
            $insolvencyConfirmationField = $dataFieldset->get('insolvencyConfirmation');
            $insolvencyConfirmationField->setLabel(
                'application_previous-history_financial-history.insolvencyConfirmation.title.ni'
            );
        }

        return $form;
    }
}

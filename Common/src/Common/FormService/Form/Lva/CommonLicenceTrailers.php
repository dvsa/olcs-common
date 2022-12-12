<?php

/**
 * Common Licence Trailers
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;

/**
 * Abstract Trailers
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommonLicenceTrailers extends AbstractFormService
{
    /**
     * Get form
     *
     * @param Request $request
     * @param TableBuilder $table
     * @return \Laminas\Form\Form
     */
    public function getForm($request, $table)
    {
        $form = $this->getFormHelper()->createFormWithRequest('Lva\Trailers', $request);
        $this->alterForm($form, $table);

        return $form;
    }

    /**
     * Generic form alterations
     *
     * @param \Laminas\Form\Form $form
     * @param TableBuilder $table
     * @return \Laminas\Form\Form
     */
    protected function alterForm($form, $table)
    {
        $form->get('table')->get('table')->setTable($table);
        $this->getFormHelper()->remove($form, 'form-actions->saveAndContinue');

        $saveButton = $form->get('form-actions')->get('save');
        $saveButton->setAttribute('class', 'govuk-button');
        return $form;
    }
}

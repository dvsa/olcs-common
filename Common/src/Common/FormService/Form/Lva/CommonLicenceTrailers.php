<?php

namespace Common\FormService\Form\Lva;

use Common\Service\Helper\FormHelperService;
use Common\Service\Table\TableBuilder;

/**
 * Abstract Trailers
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommonLicenceTrailers
{
    protected FormHelperService $formHelper;

    public function __construct(FormHelperService $formHelper)
    {
        $this->formHelper = $formHelper;
    }

    /**
     * Get form
     *
     * @param Request $request
     * @param TableBuilder $table
     * @return \Laminas\Form\Form
     */
    public function getForm($request, $table)
    {
        $form = $this->formHelper->createFormWithRequest('Lva\Trailers', $request);
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
        $this->formHelper->remove($form, 'form-actions->saveAndContinue');

        $saveButton = $form->get('form-actions')->get('save');
        $saveButton->setAttribute('class', 'govuk-button');
        return $form;
    }
}

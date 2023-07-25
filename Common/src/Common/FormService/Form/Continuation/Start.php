<?php

namespace Common\FormService\Form\Continuation;

use Common\Form\Form;
use Common\Form\Model\Form\Continuation\Start as StartForm;
use Common\Service\Helper\FormHelperService;

/**
 * Continuation start form
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class Start
{
    protected FormHelperService $formHelper;

    public function __construct(FormHelperService $formHelper)
    {
        $this->formHelper = $formHelper;
    }

    /**
     * Get form
     *
     * @return Form
     */
    public function getForm()
    {
        $form = $this->formHelper->createForm(StartForm::class);

        return $form;
    }
}

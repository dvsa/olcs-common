<?php

namespace Common\FormService\Form\Lva;

use Common\Service\Helper\FormHelperService;

/**
 * Safety Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Safety
{
    protected FormHelperService $formHelper;

    public function __construct(FormHelperService $formHelper)
    {
        $this->formHelper = $formHelper;
    }

    /**
     * Returns form
     *
     * @return \Laminas\Form\Form
     */
    public function getForm()
    {
        return $this->formHelper->createForm('Lva\Safety');
    }
}

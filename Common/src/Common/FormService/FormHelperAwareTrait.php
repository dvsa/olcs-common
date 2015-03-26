<?php

/**
 * Form Helper Aware Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService;

use Common\Service\Helper\FormHelperService;

/**
 * Form Helper Aware Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait FormHelperAwareTrait
{
    protected $formHelper;

    public function setFormHelper(FormHelperService $formHelper)
    {
        $this->formHelper = $formHelper;
    }

    public function getFormHelper()
    {
        return $this->formHelper;
    }
}

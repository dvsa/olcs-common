<?php

/**
 * Form Helper Aware Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService;

use Common\Service\Helper\FormHelperService;

/**
 * Form Helper Aware Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface FormHelperAwareInterface
{
    public function setFormHelper(FormHelperService $formHelper);

    public function getFormHelper();
}

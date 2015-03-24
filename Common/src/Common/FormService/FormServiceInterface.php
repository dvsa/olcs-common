<?php

/**
 * Form Service Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService;

use Common\FormService\FormServiceManager;

/**
 * Form Service Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface FormServiceInterface
{
    public function setFormServiceLocator(FormServiceManager $formServiceLocator);

    public function getFormServiceLocator();
}

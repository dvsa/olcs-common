<?php

/**
 * Abstract Form Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form;

use Common\FormService\FormServiceInterface;
use Common\FormService\FormHelperAwareInterface;
use Common\FormService\FormHelperAwareTrait;
use Common\FormService\FormServiceManager;

/**
 * Abstract Form Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractFormService implements FormServiceInterface, FormHelperAwareInterface
{
    use FormHelperAwareTrait;

    protected $formServiceLocator;

    public function setFormServiceLocator(FormServiceManager $formServiceLocator)
    {
        $this->formServiceLocator = $formServiceLocator;
    }

    public function getFormServiceLocator()
    {
        return $this->formServiceLocator;
    }
}

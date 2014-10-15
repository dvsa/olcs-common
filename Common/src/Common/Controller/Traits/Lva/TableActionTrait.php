<?php

/**
 * Generic Lva Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\Lva;

use Zend\Form\Form;

/**
 * Generic Lva Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait GenericLvaTrait
{
    abstract protected function getRequest();
    abstract protected function getServiceLocator();
    abstract protected function completeSection($reference);
    abstract protected function render($titleSuffix, Form $form = null);
}

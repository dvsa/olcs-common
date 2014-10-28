<?php

/**
 *
 */
namespace Common\Controller\Lva\Traits;

use Zend\Form\Form;

/**
 */
trait ApplicationOperatingCentresControllerTrait
{
    protected function getDocumentProperties()
    {
        return array(
            'application' => $this->getIdentifier(),
            'licence' => $this->getLicenceId()
        );
    }
}

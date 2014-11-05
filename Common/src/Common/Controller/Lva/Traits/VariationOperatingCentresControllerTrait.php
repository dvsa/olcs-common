<?php

/**
 * Common variation OC controller logic
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use Zend\Form\Form;

/**
 * Common variation OC controller logic
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
trait VariationOperatingCentresControllerTrait
{
    protected function getDocumentProperties()
    {
        return array(
            'application' => $this->getIdentifier(),
            'licence' => $this->getLicenceId()
        );
    }

    protected function getLvaEntity()
    {
        return 'Entity\Application';
    }

    protected function getLvaOperatingCentreEntity()
    {
        return 'Entity\ApplicationOperatingCentre';
    }
}

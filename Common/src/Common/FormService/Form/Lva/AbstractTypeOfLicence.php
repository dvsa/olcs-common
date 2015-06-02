<?php

/**
 * Abstract Type Of Licence Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;

/**
 * Abstract Type Of Licence Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractTypeOfLicence extends AbstractFormService
{
    public function getForm()
    {
        return $this->getFormHelper()->createForm('Lva\TypeOfLicence');
    }
}

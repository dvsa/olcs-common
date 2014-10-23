<?php

/**
 * PreviousHistoryLicenceHistoryLicenceValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;

/**
 * PreviousHistoryLicenceHistoryLicenceValidator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceHistoryLicenceValidator extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'noLicence' => 'Please add at least one licence'
    );

    /**
     * Custom validation for licence field
     *
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function isValid($value, $context = array())
    {
        if ($context['table']['rows'] < 1 && $value == 'Y') {

            $this->error('noLicence');
            return false;
        }

        return true;
    }
}

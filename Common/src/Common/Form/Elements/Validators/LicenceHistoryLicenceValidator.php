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
        'noLicence' => 'oneLicenceRequiredError',
        'noConviction' => 'oneConvictionRequiredError'
    );

    private $name;

    public function __construct($options = array())
    {
        $this->name = isset($options['name']) ? $options['name'] : 'noLicence';
        parent::__construct(array());
    }

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

            $this->error($this->name);
            return false;
        }

        return true;
    }
}

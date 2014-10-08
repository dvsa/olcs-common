<?php

/**
 * Type Of Licence Operator Type Validator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators\Lva;

use Common\Service\Entity\LicenceService;
use Zend\Validator\AbstractValidator;

/**
 * Type Of Licence Operator Type Validator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TypeOfLicenceOperatorTypeValidator extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'invalid-operator-type' => 'invalid-operator-type'
    );

    /**
     * Is valid
     *
     * @param string $value
     * @param array $context
     */
    public function isValid($value, $context = array())
    {
        if ($context['operator-location'] === 'Y' && $value === LicenceService::LICENCE_CATEGORY_PSV) {
            $this->error('invalid-operator-type');
            return false;
        }

        return true;
    }
}

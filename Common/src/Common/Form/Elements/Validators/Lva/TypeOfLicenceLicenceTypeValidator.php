<?php

/**
 * Type Of Licence Licence Type Validator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators\Lva;

use Common\Service\Entity\LicenceService;
use Zend\Validator\AbstractValidator;

/**
 * Type Of Licence Licence Type Validator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TypeOfLicenceLicenceTypeValidator extends AbstractValidator
{
    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'invalid-licence-type' => 'invalid-licence-type'
    );

    /**
     * Is valid
     *
     * @param string $value
     * @param array $context
     */
    public function isValid($value, $context = array())
    {
        if ($context['operator-type'] === LicenceService::LICENCE_CATEGORY_GOODS_VEHICLE
            && $value == LicenceService::LICENCE_TYPE_SPECIAL_RESTRICTED
        ) {
            $this->error('invalid-licence-type');
            return false;
        }

        return true;
    }
}

<?php

/**
 * Additional Psv Discs Filter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Common\Form\Elements\Validators\AdditionalPsvDiscsValidator;
use Zend\Validator;

/**
 * Additional Psv Discs Filter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AdditionalPsvDiscs extends Text implements InputProviderInterface
{
    protected $continueIfEmpty = true;
    protected $allowEmpty = false;

    /**
     * Get a list of validators
     *
     * @return array
     */
    protected function getValidators()
    {
        return array(
            new Validator\Digits(),
            new Validator\GreaterThan(0),
            new AdditionalPsvDiscsValidator()
        );
    }
}

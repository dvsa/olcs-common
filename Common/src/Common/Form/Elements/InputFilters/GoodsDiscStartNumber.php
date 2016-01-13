<?php

/**
 * GoodsDiscStartNumber
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Zend\Validator as ZendValidator;
use Common\Form\Elements\Validators\GoodsDiscStartNumberValidator;

/**
 * GoodsDiscStartNumber
 */
class GoodsDiscStartNumber extends Text implements InputProviderInterface
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
            new ZendValidator\Digits(),
            new GoodsDiscStartNumberValidator()
        );
    }
}

<?php

/**
 * Text Max 1024 Required
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Text Max 1024 Required
 */
class TextMax1024Required extends TextMax1024 implements InputProviderInterface
{
    protected $required = true;
    protected $allowEmpty = false;
}

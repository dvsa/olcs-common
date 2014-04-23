<?php

/**
 * Text Max 70 Required
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Text Max 75 Required
 */
class TextMax75Required extends TextMax75 implements InputProviderInterface
{
    protected $required = true;
}

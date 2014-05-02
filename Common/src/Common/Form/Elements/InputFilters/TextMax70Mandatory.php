<?php

/**
 * Text Max 70 Required and Mandatory
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Text Max 70 Required
 */
class TextMax70Mandatory extends TextMax70Required implements InputProviderInterface
{
    protected $allowEmpty = false;
}

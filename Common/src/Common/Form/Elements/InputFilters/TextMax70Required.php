<?php

/**
 * Text Max 70 Required
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Text Max 70 Required
 */
class TextMax70Required extends TextMax70 implements InputProviderInterface
{
    protected $required = true;
}

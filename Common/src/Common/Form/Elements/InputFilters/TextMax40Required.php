<?php

/**
 * Text Max 40 Required
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Text Max 40 Required
 */
class TextMax40Required extends TextMax40 implements InputProviderInterface
{
    protected $required = true;
}

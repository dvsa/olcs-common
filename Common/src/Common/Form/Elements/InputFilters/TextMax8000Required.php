<?php

/**
 * Text Max 4000 Required
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Text Max 4000 Required
 */
class TextMax8000Required extends TextMax8000 implements InputProviderInterface
{
    protected $required = true;
}

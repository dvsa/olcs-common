<?php

/**
 * Text Max 255
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Text Max 255
 */
class TextMax255Required extends TextMax255 implements InputProviderInterface
{
    protected $required = true;
    protected $allowEmpty = false;
}

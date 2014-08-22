<?php

/**
 * Text Max 1024
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Text Max 1024
 */
class TextMax1024 extends TextArea implements InputProviderInterface
{
    protected $max = 1024;
}

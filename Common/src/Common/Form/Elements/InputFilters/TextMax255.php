<?php

/**
 * Text Max 255
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Text Max 255
 */
class TextMax255 extends Text implements InputProviderInterface
{
    protected $max = 255;
}

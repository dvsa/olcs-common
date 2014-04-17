<?php

/**
 * Text Max 70
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Text Max 75
 */
class TextMax75 extends Text implements InputProviderInterface
{
    protected $max = 75;
}

<?php

/**
 * Text Max 40
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Text Max 40
 */
class TextMax40 extends Text implements InputProviderInterface
{
    protected $max = 40;
}

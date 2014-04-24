<?php

/**
 * Text Max 20
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Text Max 20
 */
class TextMax20 extends Text implements InputProviderInterface
{
    protected $max = 20;
}

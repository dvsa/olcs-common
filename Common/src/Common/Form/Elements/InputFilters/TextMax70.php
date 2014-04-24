<?php

/**
 * Text Max 70
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Text Max 70
 */
class TextMax70 extends Text implements InputProviderInterface
{
    protected $max = 70;
}

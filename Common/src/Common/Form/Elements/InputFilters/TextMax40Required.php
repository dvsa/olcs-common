<?php

/**
 * Text Max 40 Required
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Text Max 40 Required
 * DOES NOT extend TextMax40 because that extends Text, which in turn sets
 * required to be false
 */
class TextMax40Required extends TextRequired implements InputProviderInterface
{
    protected $max = 40;
}

<?php

/**
 * Text Max 4000
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Text Max 4000
 */
class TextMax8000 extends Textarea implements InputProviderInterface
{
    protected $max = '8000';
}

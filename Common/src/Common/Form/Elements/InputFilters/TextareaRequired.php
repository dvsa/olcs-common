<?php

/**
 * Textarea Required
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Textarea Required
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TextareaRequired extends Textarea implements InputProviderInterface
{
    protected $required = true;
}

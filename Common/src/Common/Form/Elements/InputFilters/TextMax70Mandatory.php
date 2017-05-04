<?php

namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * @deprecated This does not get used and must be removed as in: OLCS-15198
 *
 * Text Max 70 Required
 */
class TextMax70Mandatory extends TextMax70Required implements InputProviderInterface
{
    protected $allowEmpty = false;
}

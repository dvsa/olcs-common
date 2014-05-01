<?php

/**
 * Text Required
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * Text Required
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TextRequired extends Text implements InputProviderInterface
{
    protected $required = true;
    protected $allowEmpty = false;
}

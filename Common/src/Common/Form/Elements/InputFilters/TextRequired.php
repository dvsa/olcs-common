<?php
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * @deprecated This does not get used and must be removed as in: OLCS-15198
 *
 * Text Required
 */
class TextRequired extends Text implements InputProviderInterface
{
    protected $required = true;
}

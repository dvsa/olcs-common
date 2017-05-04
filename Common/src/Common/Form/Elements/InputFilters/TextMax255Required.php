<?php
namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 * @deprecated This does not get used and must be removed as in: OLCS-15198
 *
 * Text max 255 required
 */
class TextMax255Required extends TextMax255 implements InputProviderInterface
{
    protected $required = true;
    protected $allowEmpty = false;
}

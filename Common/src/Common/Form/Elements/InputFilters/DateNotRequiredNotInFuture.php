<?php
/**
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 *
 */

namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Zend\Validator\Date as DateValidator;

/**
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 *
 */
class DateNotRequiredNotInFuture extends DateNotInFuture implements InputProviderInterface
{
    protected $required = false;
}

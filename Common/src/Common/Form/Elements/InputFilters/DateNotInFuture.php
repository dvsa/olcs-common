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
class DateNotInFuture extends DateRequired implements InputProviderInterface
{

    public function getValidators()
    {
        return array(
            new DateValidator(array('format' => 'Y-m-d')),
            new \Common\Form\Elements\Validators\DateNotInFuture()
        );
    }
}

<?php
/**
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */

namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Zend\Validator\Date as DateValidator;

/**
 * Checks statement request date is not before the stop date
 */
class StopDateBeforeRequestDate extends DateRequired implements InputProviderInterface
{

    public function getValidators()
    {
        return array(
            new \Common\Form\Elements\Validators\DateNotInFuture(),
            new \Common\Form\Elements\Validators\DateLessThanOrEqual('requestedDate'),
            new DateValidator(array('format' => 'Y-m-d'))
        );
    }
}

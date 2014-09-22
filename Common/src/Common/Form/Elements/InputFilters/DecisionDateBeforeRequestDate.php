<?php
/**
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */

namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;
use Zend\Validator\Date as DateValidator;

/**
 * Checks date is not before the request date
 */
class DecisionDateBeforeRequestDate extends DateRequired implements
    InputProviderInterface
{

    public function getValidators()
    {
        return array(
            new \Common\Form\Elements\Validators\DateNotInFuture(),
            new \Common\Form\Elements\Validators\DateGreaterThanOrEqual('requestDate'),
            new DateValidator(array('format' => 'Y-m-d'))
        );
    }
}

<?php
/**
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 *
 */

namespace Common\Form\Elements\InputFilters;

use Laminas\InputFilter\InputProviderInterface as InputProviderInterface;
use Laminas\Validator\Date as DateValidator;

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
            ['name' => 'Date', 'options' => array('format' => 'Y-m-d')],
            ['name' => '\Common\Form\Elements\Validators\DateNotInFuture']
        );
    }
}

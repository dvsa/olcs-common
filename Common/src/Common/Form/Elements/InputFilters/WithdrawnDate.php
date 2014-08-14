<?php
/**
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 *
 */

namespace Common\Form\Elements\InputFilters;

use Zend\InputFilter\InputProviderInterface as InputProviderInterface;

/**
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 *
 * Checks that if a withdrawn checkbox is ticked then the corresponding date is also filled in
 *
 */
class WithdrawnDate extends DateNotRequiredNotInFuture implements InputProviderInterface
{
    public function getValidators()
    {
        return array(
            new \Common\Form\Elements\Validators\WithdrawnDate()
        );
    }
}
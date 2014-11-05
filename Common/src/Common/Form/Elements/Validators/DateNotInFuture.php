<?php

/**
 * Checks a date is not in the future
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator as AbstractValidator;

/**
 * Checks a date is not in the future
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DateNotInFuture extends AbstractValidator
{
    /**
     * Error codes
     * @const string
     */
    const IN_FUTURE = 'inFuture';

    /**
     * Error messages
     * @var array
     */
    protected $messageTemplates = array(
        self::IN_FUTURE => "This date is not allowed to be in the future",
    );

    /**
     * Returns true if the date is not in the future
     *
     * @param  mixed $value
     * @return bool
     * @throws Exception\RuntimeException if the token doesn't exist in the context array
     */
    public function isValid($value)
    {
        $date = strtotime($value);
        $today = strtotime(date('Y-m-d'));

        if ($date > $today) {
            $this->error(self::IN_FUTURE);
            return false;
        }

        return true;
    }
}

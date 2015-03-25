<?php

/**
 * Custom validator for confirming an email address
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\Identical;

/**
 * Custom validator for confirming an email address
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class EmailConfirm extends Identical
{
    /**
     * Error messages
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_SAME      => "Email addresses don't match",
        self::MISSING_TOKEN => 'No email address was provided to match against',
    );
}

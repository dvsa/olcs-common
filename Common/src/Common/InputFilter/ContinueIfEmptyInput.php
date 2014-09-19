<?php

namespace Common\InputFilter;

use Zend\InputFilter\Input;

/**
 * Class ContinueIfEmptyInput
 * @package Common\InputFilter
 */
class ContinueIfEmptyInput extends Input
{
    /**
     * @var bool
     */
    protected $continueIfEmpty = true;
}

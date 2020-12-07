<?php

namespace Common\InputFilter;

use Laminas\InputFilter\Input as ZendInput;

/**
 * Class ContinueIfEmptyInput
 * @package Common\InputFilter
 */
class ContinueIfEmptyInput extends ZendInput
{
    /**
     * @var bool
     */
    protected $continueIfEmpty = true;
}

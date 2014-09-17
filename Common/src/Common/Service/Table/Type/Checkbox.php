<?php

/**
 * Checkbox type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Service\Table\Type;

/**
 * Checkbox type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Checkbox extends Selector
{
    protected $format = '<input type="checkbox" name="%s[]" value="%s" />';
}

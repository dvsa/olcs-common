<?php

/**
 * DateSelect
 *
 * @author Someone <someone@valtech.co.uk>
 */
namespace Common\Form\Elements\Custom;

use Zend\Form\Element as ZendElement;

/**
 * DateSelect
 *
 * @author Someone <someone@valtech.co.uk>
 */
class DateSelect extends ZendElement\DateSelect
{
    use Traits\YearDelta;
}

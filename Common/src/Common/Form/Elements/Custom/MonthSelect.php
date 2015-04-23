<?php

/**
 * Month Select
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Custom;

use Zend\Form\Element as ZendElement;

/**
 * Month Select
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class MonthSelect extends ZendElement\MonthSelect
{
    use Traits\YearDelta;
}

<?php

/**
 * Check Date Rule
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessRule\Rule;

use Common\BusinessRule\BusinessRuleInterface;

/**
 * Check Date Rule
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CheckDate implements BusinessRuleInterface
{
    public function validate(array $date)
    {
        if (checkdate((int)$date['month'], (int)$date['day'], (int)$date['year'])) {

            return sprintf('%s-%s-%s', $date['year'], $date['month'], $date['day']);
        }

        return null;
    }
}

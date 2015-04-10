<?php

/**
 * BirthDate Rule
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Common\BusinessRule\Rule;

use Common\BusinessRule\BusinessRuleInterface;
use Common\BusinessRule\Rule\CheckDate;

/**
 * BirthDate Rule - essentially checks that date is Not in future
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class BirthDate extends CheckDate
{
    public function validate(array $date)
    {
        $checkedDate = parent::validate($date);
        $checkedDateTime = new \DateTime($checkedDate);
        $now = date('Y-m-d');
        $today = new \DateTime($now);
        if ($checkedDateTime < $today) {
            return $checkedDate;
        }

        return null;
    }
}

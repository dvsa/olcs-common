<?php

/**
 * Locked Date
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Common\BusinessRule\Rule;

use Common\BusinessRule\BusinessRuleInterface;
use Common\BusinessRule\BusinessRuleAwareInterface;
use Common\BusinessRule\BusinessRuleAwareTrait;

/**
 * Locked Date Rule
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class LockedDate implements BusinessRuleInterface, BusinessRuleAwareInterface
{
    use BusinessRuleAwareTrait;

    /**
     * @param $accountDisabled
     * @return null|string
     */
    public function validate($accountDisabled)
    {
        if ($accountDisabled == 'Y') {
            $date = date('Y-m-d H:i:s');
            if (is_string($date)) {
                return $date;
            }
        }
        return null;
    }
}

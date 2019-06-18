<?php

namespace Common\Util;

/**
 * Check if given ID is lower than the auto-inc value - therefore an ECMT app.
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class IsEcmtId
{
    const IRHP_APPLICATION_AUTO_INC_VAL = 100000;

    public static function isEcmtId($id)
    {
        return $id < self::IRHP_APPLICATION_AUTO_INC_VAL;
    }
}

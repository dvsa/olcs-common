<?php

/**
 * Date Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

/**
 * Date Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DateHelperService extends AbstractHelperService
{
    public function getDate($format = 'Y-m-d')
    {
        return date($format);
    }

    public function getDateObject($time = "now")
    {
        return new \DateTime($time);
    }

    /**
     * Convert DateSelect style array data to a DateTime object
     * @param array $date
     * @return \DateTime
     */
    public function getDateObjectFromArray(array $date)
    {
        $obj = new \DateTime();
        $obj->setDate($date['year'], $date['month'], $date['day']);
        return $obj;
    }
}

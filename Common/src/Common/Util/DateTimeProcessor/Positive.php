<?php
namespace Common\Util\DateTimeProcessor;

use DateTime as PHPDateTime;
use Zend\ServiceManager\ServiceLocatorInterface as ZendServiceLocatorInterface;
use Common\Service\Data\PublicHoliday as PublicHolidayService;

/**
 * Date Time Processor
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class Positive extends DateTimeProcessorAbstract
{
    /**
     * Change date to add/subtract working days.
     *
     * @param PHPDateTime|string $endDate
     * @param integer $workingDays
     *
     * @return PHPDateTime
     */
    public function processWorkingDays($endDate, $workingDays)
    {
        $endDate = $this->checkDate($endDate);
        for ($i = 0; $i < $workingDays; $i++) {
            $this->dateAddDays($endDate, 1, true);
        }

        return $endDate;
    }

    /**
     * Processes the public holidays.
     *
     * @param PHPDateTime $date
     * @param PHPDateTime $endDate
     * @param boolean $we
     *
     * @return PHPDateTime Unnecessary as it's passin by reference in the first place.
     */
    public function processPublicHolidays(PHPDateTime $date, PHPDateTime $endDate, $we)
    {
        $publicHolidays = $this->getPublicHolidaysArray($date, $endDate);

        // ensure in date order to avoid cross over missed dates
        uasort(
            $publicHolidays,
            function ($a, $b) {
                return strtotime($a) -
                strtotime($b);
            }
        );

        foreach ($publicHolidays as $publicHoliday) {

            $publicHolidayDateTime = $this->checkDate($publicHoliday);

            if ($publicHolidayDateTime >= $date && $publicHolidayDateTime <= $endDate) {

                $this->dateAddDays($endDate, 1, $we);
            }
        }

        return $endDate;
    }

    /**
     * Adds days to a date.
     *
     * @param PHPDateTime $date
     * @param integer $days
     * @return string
     */
    public function dateAddDays(PHPDateTime $date, $days, $considerWeekend = false)
    {
        $dateAddString = $days . ' days';

        $date->add(\DateInterval::createFromDateString($dateAddString));

        if ($considerWeekend === true) {

            if ($date->format('N') == 6) {

                $this->dateAddDays($date, 2, false);

            } elseif ($date->format('N') == 7) {

                $this->dateAddDays($date, 1, false);
            }

        }

        return $date;
    }
}

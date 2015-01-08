<?php
namespace Common\Util\DateTimeProcessor;

use DateTime as PHPDateTime;
use Zend\ServiceManager\ServiceLocatorInterface as ZendServiceLocatorInterface;
use Common\Service\Data\PublicHoliday as PublicHolidayService;

/**
 * Date Time Processor
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
abstract class DateTimeProcessorAbstract
{
    /**
     * @var \Common\Service\Data\PublicHoliday
     */
    protected $publicHolidayService;

    /**
     * Change date to add/subtract working days.
     *
     * @param PHPDateTime|string $endDate
     * @param integer $workingDays
     *
     * @return PHPDateTime
     */
    abstract public function processWorkingDays($endDate, $workingDays);

    /**
     * Processes the public holidays.
     *
     * @param PHPDateTime $date
     * @param PHPDateTime $endDate
     * @param boolean $we
     *
     * @return PHPDateTime Unnecessary as it's passin by reference in the first place.
     */
    abstract public function processPublicHolidays(PHPDateTime $date, PHPDateTime $endDate, $we);

    /**
     * Gets a list of bank holidays
     *
     * @return multitype:string
     */
    public function getPublicHolidaysArray(PHPDateTime $date, PHPDateTime $endDate)
    {
        return $this->getPublicHolidayService()->fetchpublicHolidaysArray($date, $endDate);
    }

    /**
     * Adds days to a date.
     *
     * @param PHPDateTime $date
     * @param integer $days
     * @return string
     */
    abstract public function dateAddDays(PHPDateTime $date, $days, $considerWeekend = false);

    /**
     * Checks that
     *
     * @param unknown $date
     * @return \DateTime|DateTime
     */
    public function checkDate($date)
    {
        if ($date instanceof PHPDateTime) {
            return $date;
        }

        return $this->createDateTimeFromString($date);
    }

    /**
     * Creates a DateTime object from a string containing a date.
     *
     * @param string $date String containing an strtotime compatible date.
     *
     * @return \DateTime
     */
    public function createDateTimeFromString($date)
    {
        $ts = strtotime($date);

        $time = mktime(0, 0, 0, date("n", $ts), date("j", $ts), date("Y", $ts));

        $dateTime = date(PHPDateTime::ISO8601, $time);

        //echo $dateTime . PHP_EOL;

        return PHPDateTime::createFromFormat(PHPDateTime::ISO8601, $dateTime);
    }

    /**
     * Getter.
     *
     * @return \Common\Service\Data\PublicHoliday
     */
    public function getPublicHolidayService()
    {
        return $this->publicHolidayService;
    }

    /**
     * Setter.
     *
     * @param PublicHolidayService $publicHolidayService
     * @return \Common\Util\DateTime
     */
    public function setPublicHolidayService(PublicHolidayService $publicHolidayService)
    {
        $this->publicHolidayService = $publicHolidayService;
        return $this;
    }
}

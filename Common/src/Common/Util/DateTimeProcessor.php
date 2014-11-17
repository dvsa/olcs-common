<?php
namespace Common\Util;

use DateTime as PHPDateTime;
use Zend\ServiceManager\ServiceLocatorInterface as ZendServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface as ZendFactoryInterface;
use Common\Service\Data\PublicHoliday as PublicHolidayService;

class DateTimeProcessor implements ZendFactoryInterface
{
    /**
     *
     * @var \Common\Service\Data\PublicHoliday
     */
    protected $publicHolidayService;

    /**
     * Calculates a date.
     *
     * @param string|PHPDateTime $date Should be
     * @param integer $days The number of days to offset (can be a negative number)
     * @param boolean $we Should weekend days be considered/excluded
     * @param boolean $bh Should public holidays be considered/excluded
     */
    public function calculateDate($date, $days, $we = false, $bh = false)
    {
        $date = $this->checkDate($date);
        $endDate = clone $date;

        if (true === $we) {
            $this->processWorkingDays($endDate, $days);
        } else {
            $this->dateAddDays($endDate, $days);
        }

        if (true === $bh) {
            $this->processPublicHolidays($date, $endDate, $we);
        }

        return $endDate->format('Y-m-d');
    }

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

        $workingWeeks = floor($workingDays / 5);

        $totalDays = $workingWeeks * 7;

        $daysLeft = $workingDays - ($workingWeeks * 5);

        $this->dateAddDays($endDate, $totalDays, true);

        while ($daysLeft) {

            $this->dateAddDays($endDate, 1, true);

            $daysLeft--;
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
        $publicHolidays = $this->getPublicHolidaysArray();

        foreach ($publicHolidays as $publicHoliday) {

            $publicHolidayDateTime = $this->checkDate($publicHoliday);

            if ($publicHolidayDateTime >= $date && $publicHolidayDateTime <= $endDate) {

                $this->dateAddDays($endDate, 1, $we);
            }
        }

        return $endDate;
    }

    /**
     * Gets a list of bank holidays
     *
     * @return multitype:string
     */
    public function getPublicHolidaysArray()
    {
        return $this->getPublicHolidayService()->fetchpublicHolidaysArray();

        return [
            '2014-12-25',
            '2014-12-26',
            '2015-01-01',
            '2015-04-03',
            '2015-04-06',
        ];
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

            } else if ($date->format('N') == 7) {

                $this->dateAddDays($date, 1, false);
            }

        }

        return $date;
    }

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
        $dateTime = date(PHPDateTime::ISO8601, strtotime($date));

        return PHPDateTime::createFromFormat(PHPDateTime::ISO8601, $dateTime);
    }

    /**
     * Create service fatory method.
     *
     * @param ZendServiceLocatorInterface $serviceLocator
     * @return \Common\Util\DateTime
     */
    public function createService(ZendServiceLocatorInterface $serviceLocator)
    {
        $this->setPublicHolidayService($serviceLocator->get('DataServiceManager')->get('Common\Service\Data\PublicHoliday'));

        return $this;
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
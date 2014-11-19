<?php
namespace Common\Util;

use DateTime as PHPDateTime;
use Zend\ServiceManager\ServiceLocatorInterface as ZendServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface as ZendFactoryInterface;
use Common\Service\Data\PublicHoliday as PublicHolidayService;
use Common\Util\DateTimeProcessor\DateTimeProcessorAbstract as AbstractProcessor;
use Common\Util\DateTimeProcessor\Positive as PositiveProcessor;
use Common\Util\DateTimeProcessor\Negative as NegativeProcessor;

/**
 * Date Time Processor
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class DateTimeProcessor implements ZendFactoryInterface
{
    protected $processors = [];

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
        if ($days > 0) {
            $processor = $this->getPositiveProcessor();
        } else {
            $processor = $this->getNegativeProcessor();
        }

        $date = $processor->checkDate($date);
        $endDate = clone $date;

        if (true === $we) {
            $processor->processWorkingDays($endDate, $days);
        } else {
            $processor->dateAddDays($endDate, $days);
        }

        if (true === $bh) {
            $processor->processPublicHolidays($date, $endDate, $we);
        }

        return $endDate->format('Y-m-d');
    }

    /**
     * Create service fatory method.
     *
     * @param ZendServiceLocatorInterface $serviceLocator
     * @return \Common\Util\DateTime
     */
    public function createService(ZendServiceLocatorInterface $serviceLocator)
    {
        $publicHolidayService = $serviceLocator->get('DataServiceManager')->get('Common\Service\Data\PublicHoliday');

        $this->setPositiveProcessor(new PositiveProcessor())
            ->getPositiveProcessor()->setPublicHolidayService($publicHolidayService);

        $this->setNegativeProcessor(new NegativeProcessor())
            ->getNegativeProcessor()->setPublicHolidayService($publicHolidayService);

        return $this;
    }

    /**
     * @param PositiveProcessor $processor
     * @return \Common\Util\DateTimeProcessor
     */
    public function setPositiveProcessor(PositiveProcessor $processor)
    {
        $this->processors['positive'] = $processor;
        return $this;
    }

    /**
     * @return PositiveProcessor
     */
    public function getPositiveProcessor()
    {
        return $this->processors['positive'];
    }

    /**
     * @param NegativeProcessor $processor
     * @return \Common\Util\DateTimeProcessor
     */
    public function setNegativeProcessor(NegativeProcessor $processor)
    {
        $this->processors['negative'] = $processor;
        return $this;
    }

    /**
     * @return NegativeProcessor
     */
    public function getNegativeProcessor()
    {
        return $this->processors['negative'];
    }
}

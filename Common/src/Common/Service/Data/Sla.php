<?php

namespace Common\Service\Data;

use Common\Util\RestClient;
use Common\Util\DateTimeProcessor as DateTimeProcessor;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Sla
 *
 * @package Common\Service
 */
class Sla extends AbstractData
{
    protected $serviceName = 'Sla';

    /**
     * @var \Common\Util\DateTimeProcessor
     */
    protected $dateTimeProcessor;

    protected $context = array();

    public function getTargetDate($category, $name, $context = null)
    {
        if (null === $context) {
            $context = $this->getContext($category);
        }

        $rules = $this->fetchBusRules($category);

        foreach ($rules as $rule) {

            if ($rule['field'] == $name) {

                if (!array_key_exists($rule['compareTo'], $context) || empty($context[$rule['compareTo']])) {
                    return null;
                }

                $dateToCompare = \DateTime::createFromFormat('Y-m-d', $context[$rule['compareTo']]);

                $effectiveFrom = \DateTime::createFromFormat('Y-m-d', $rule['effectiveFrom']);
                $effectiveTo = \DateTime::createFromFormat('Y-m-d', $rule['effectiveTo']);

                if ($dateToCompare >= $effectiveFrom && (false === $effectiveTo || $dateToCompare <= $effectiveTo)) {

                    $outputDate = $this->getTimeDateProcessor()->calculateDate(
                        $context[$rule['compareTo']],
                        $rule['days'],
                        (bool)$rule['weekend'],
                        (bool)$rule['publicHoliday']
                    );

                    return $outputDate;
                }
            }
        }

        throw new \LogicException('No rule exists for this context');
    }

    public function setContext($category, array $context)
    {
        $this->context[$category] = $context;
        return $this;
    }

    public function getContext($category)
    {
        if (array_key_exists($category, $this->context)) {
            return $this->context[$category];
        }

        throw new \Exception('No context for category ' . $category);
    }

    /**
     * Ensures only a single call is made to the backend for each dataset
     *
     * @param $category
     * @return array
     */
    public function fetchBusRules($category)
    {
        if (is_null($this->getData($category))) {
            $data = $this->getRestClient()->get('', ['limit' => 1000, 'category' => $category]);
            if (empty($data)) {
                return null;
            }

            if (!isset($data['Results']) || empty($data['Results'])) {
                return null;
            }

            $this->setData($category, $data['Results']);
        }

        return $this->getData($category);
    }

    /**
     * Setter for DateTimeProcessor.
     *
     * @param DateTimeProcessor $dateTimeProcessor
     * @return \Common\Service\Data\Sla
     */
    public function setTimeDateProcessor(DateTimeProcessor $dateTimeProcessor)
    {
        $this->dateTimeProcessor = $dateTimeProcessor;
        return $this;
    }

    /**
     * Getter for DateTimeProcessor.
     *
     * @return \Common\Util\DateTimeProcessor
     */
    public function getTimeDateProcessor()
    {
        return $this->dateTimeProcessor;
    }

    /**
     * Adds days to a date.
     *
     * @param string $input
     * @param integer $days
     * @return string
     */
    public function dateAddDays($date, $days)
    {
        if (empty($date)) {
            return null;
        }

        $date = \DateTime::createFromFormat(
            \DateTime::ISO8601, date(\DateTime::ISO8601, strtotime($date))
        );

        $dateAddString = $days . ' days';

        $date->add(\DateInterval::createFromDateString($dateAddString));

        return $date->format('Y-m-d');
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @deprecated
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        parent::createService($serviceLocator);

        $this->setTimeDateProcessor(
            $serviceLocator->get('Common\Util\DateTimeProcessor')
        );

        return $this;
    }
}

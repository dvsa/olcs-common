<?php

namespace Common\Service\Data;

use Common\Util\RestClient;

/**
 * Class Sla
 *
 * @package Common\Service
 */
class Sla extends AbstractData
{
    protected $serviceName = 'Sla';

    protected $context = array();

    public function getTargetDate($category, $name, $context = null)
    {
        if (null === $context) {
            $context = $this->getContext($category);
        }

        $rules = $this->fetchBusRules($category);

        foreach ($rules as $rule) {

            if ($rule['field'] == $name) {

                $dateToCompare = \DateTime::createFromFormat('Y-m-d', $context[$rule['compareTo']]);

                $effectiveFrom = \DateTime::createFromFormat('Y-m-d', $rule['effectiveFrom']);
                $effectiveTo = \DateTime::createFromFormat('Y-m-d', $rule['effectiveTo']);

                if ($dateToCompare >= $effectiveFrom && (false === $effectiveTo || $dateToCompare <= $effectiveTo)) {
                    return $this->dateAddDays($context[$rule['compareTo']], $rule['days']);
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

            if (!isset($data['Results']) || empty($data['Results']))  {
                return null;
            }

            $this->setData($category, $data['Results']);
        }

        return $this->getData($category);
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
        $date = \DateTime::createFromFormat(
            \DateTime::ISO8601, date(\DateTime::ISO8601, strtotime($date))
        );

        $dateAddString = $days . ' days';

        $date->add(\DateInterval::createFromDateString($dateAddString));

        return $date->format('Y-m-d');
    }
}

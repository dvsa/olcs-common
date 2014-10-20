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
    protected $serviceName = 'SystemParameter';

    protected $rules;

    public function getRule($category, $name)
    {
        if (!isset($this->rules[$name])) {
            $rules = $this->fetchBusRules($category);
            $foundRule = false;

            foreach ($rules as $rule) {
                if ($rule['name'] == $name) {
                    //test effective date
                    $foundRule = $rule;
                }
            }

            $this->rules[$name] = $foundRule;
        }

        return $this->rules[$name];
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
            $this->setData($category, $data);
        }

        return $this->getData($category);
    }

    public function getTargetDate($category, $name, $context)
    {
        $rule = $this->getRule($category, $name);

        $date = \DateTime::createFromFormat(
            \DateTime::ISO8601, date(\DateTime::ISO8601,
            strtotime($context[$rule['compareTo']]))
        );

        $dateAddString = $rule['days'] . ' days';

        $date->add(\DateInterval::createFromDateString($dateAddString));

        return $context[$rule['compareTo']] + $rule['days'];
    }

    /**
     * Adds days to a date.
     *
     * @param unknown $input
     * @param unknown $days
     * @return string
     */
    public function dateAddDays($date, $days)
    {
        $date = \DateTime::createFromFormat(
            \DateTime::ISO8601, date(\DateTime::ISO8601, strtotime($date))
        );

        return $days . ' days';
    }
}

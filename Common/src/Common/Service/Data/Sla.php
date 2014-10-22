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

    public function getTargetDate($category, $name, $context)
    {

        $rules = $this->fetchBusRules($category);

        foreach ($rules as $rule) {

            if ($rule['field'] == $name) {

                $this->dateAddDays($context[$rule['compareTo']], $rule['days']);

                $dateToCompare = \DateTime::createFromFormat('Y-m-d', $context[$rule['compareTo']]);

                $effectiveFrom = \DateTime::createFromFormat('Y-m-d', $rule['effectiveFrom']);
                $effectiveTo = \DateTime::createFromFormat('Y-m-d', $rule['effectiveTo']);

                if ($dateToCompare >= $effectiveFrom) {

                    if (false === $effectiveTo || (false !== $effectiveTo && $dateToCompare <= $effectiveTo)) {

                        return $this->dateAddDays($context[$rule['compareTo']], $rule['days']);
                    }
                }
            }
        }

        throw new \LogicException('No rule exists for this context');

    }

    public function processData($in)
    {
        $outdata = [];

        foreach ($in['Results'] as $item) {
            $outdata[] = $item;
        }

        return $outdata;
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
            //die('<pre>' . print_r($data, 1));
            $this->setData($category, $data);
        }

        return $this->processData($this->getData($category));
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

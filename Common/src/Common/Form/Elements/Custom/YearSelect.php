<?php

/**
 * Year Select
 */
namespace Common\Form\Elements\Custom;

use Laminas\Form\Element as LaminasElement;

/**
 * Year Select
 */
class YearSelect extends LaminasElement\Select
{
    use Traits\YearDelta;

    /**
     * Min year to use for the select (default: current year - 100)
     *
     * @var int
     */
    protected $minYear;

    /**
     * Max year to use for the select (default: current year)
     *
     * @var int
     */
    protected $maxYear;

    /**
     * Constructor. Add two selects elements
     *
     * @param  null|int|string  $name    Optional name for the element
     * @param  array            $options Optional options for the element
     */
    public function __construct($name = null, $options = array())
    {
        $this->minYear = date('Y') - 100;
        $this->maxYear = date('Y');

        parent::__construct($name, $options);
    }

    /**
     * @param  array $options
     * @return Select
     */
    public function setOptions($options)
    {
        // set Min/Max Year based on element's options
        $minYearDelta = !empty($options['min_year_delta']) ? $options['min_year_delta'] : null;
        $maxYearDelta = !empty($options['max_year_delta']) ? $options['max_year_delta'] : null;
        $this->setMinMaxYear($minYearDelta, $maxYearDelta);

        $years = [];
        for ($i = $this->maxYear; $i >= $this->minYear; $i--) {
            $years[$i] = $i;
        }
        $options['options'] = $years;

        return parent::setOptions($options);
    }

    /**
     * @param  int $minYear
     * @return MonthSelect
     */
    public function setMinYear($minYear)
    {
        $this->minYear = $minYear;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinYear()
    {
        return $this->minYear;
    }

    /**
     * @param  int $maxYear
     * @return MonthSelect
     */
    public function setMaxYear($maxYear)
    {
        $this->maxYear = $maxYear;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxYear()
    {
        return $this->maxYear;
    }
}

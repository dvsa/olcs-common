<?php

/**
 * Year Delta
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\Elements\Custom\Traits;

/**
 * Year Delta
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait YearDelta
{
    public function setOptions($options)
    {
        parent::setOptions($options);

        if ($this->getOption('max_year_delta')) {
            $maxYear = date('Y', strtotime($this->getOption('max_year_delta') . ' years'));

            $minYear = $this->getOption('min_year_delta');

            if (isset($minYear)) {
                $minYear = date('Y', strtotime($minYear . ' years'));
            } else {
                // if there's no delta specified, initially set the minimum year
                // to the current year
                $minYear = date('Y');
            }

            $this->setMinYear($minYear);
            $this->setMaxYear($maxYear);
        }

        // This option allows us to default the date
        $defaultDate = $this->getOption('default_date');

        if ($defaultDate) {

            $dateTime = new \DateTime();

            if ($defaultDate !== 'now') {
                $dateTime->modify($defaultDate);
            }

            $this->setValue($dateTime);
        }
    }
}

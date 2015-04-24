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

        $setMaxYear = false;
        if ($this->getOption('max_year_delta')) {
            $setMaxYear = true;
            $maxYear = date('Y', strtotime($this->getOption('max_year_delta') . ' years'));
            $this->setMaxYear($maxYear);
        }

        $setMinYear = false;
        if ($this->getOption('min_year_delta')) {
            $setMinYear = true;
            $minYear = date('Y', strtotime($this->getOption('min_year_delta') . ' years'));
            $this->setMinYear($minYear);
        }

        if ($setMaxYear && !$setMinYear) {
            $this->setMinYear(date('Y'));
        }

        if ($setMinYear && !$setMaxYear) {
            $this->setMaxYear(date('Y'));
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

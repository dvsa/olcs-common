<?php

/**
 * Hours per week fieldset
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Form\Elements\Types;

use Zend\Form\Fieldset;
use Zend\Form\Element\Text;
use Common\Form\Elements\InputFilters\HoursPerDay;

/**
 * Hours per week fieldset
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class HoursPerWeek extends Fieldset
{

    /**
     * Setup the elements
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name = null, $options = [])
    {

        parent::__construct($name, $options);

        $allDays = new Fieldset('hoursPerWeekContent');

        $hoursMon = new HoursPerDay('hoursMon');
        $hoursMon->setAttributes(
            [
                'class' => 'short',
                'data-container-class' => 'inline-text',
            ]
        );
        $hoursMon->setOptions(
            [
                'label' => 'days-of-week-short-mon'
            ]
        );
        $allDays->add($hoursMon);

        $hoursTue = new HoursPerDay('hoursTue');
        $hoursTue->setAttributes(
            [
                'class' => 'short',
                'data-container-class' => 'inline-text'
            ]
        );
        $hoursTue->setOptions(
            [
               'label' => 'days-of-week-short-tue'
            ]
        );
        $allDays->add($hoursTue);

        $hoursWed = new HoursPerDay('hoursWed');
        $hoursWed->setAttributes(
            [
                'class' => 'short',
                'data-container-class' => 'inline-text'
            ]
        );
        $hoursWed->setOptions(
            [
                'label' => 'days-of-week-short-wed'
            ]
        );
        $allDays->add($hoursWed);

        $hoursThu = new HoursPerDay('hoursThu');
        $hoursThu->setAttributes(
            [
                'class' => 'short',
                'data-container-class' => 'inline-text'
            ]
        );
        $hoursThu->setOptions(
            [
                'label' => 'days-of-week-short-thu'
            ]
        );
        $allDays->add($hoursThu);

        $hoursFri = new HoursPerDay('hoursFri');
        $hoursFri->setAttributes(
            [
                'class' => 'short',
                'data-container-class' => 'inline-text'
            ]
        );
        $hoursFri->setOptions(
            [
                'label' => 'days-of-week-short-fri'
            ]
        );
        $allDays->add($hoursFri);

        $hoursSat = new HoursPerDay('hoursSat');
        $hoursSat->setAttributes(
            [
                'class' => 'short',
                'data-container-class' => 'inline-text'
            ]
        );
        $hoursSat->setOptions(
            [
                'label' => 'days-of-week-short-sat'
            ]
        );
        $allDays->add($hoursSat);

        $hoursSun = new HoursPerDay('hoursSun');
        $hoursSun->setAttributes(
            [
                'class' => 'short',
                'data-container-class' => 'inline-text'
            ]
        );
        $hoursSun->setOptions(
            [
                'label' => 'days-of-week-short-sun'
            ]
        );
        $allDays->add($hoursSun);

        $this->add($allDays);
    }

    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['subtitle'])) {
            $this->get('hoursPerWeekContent')->setLabel($options['subtitle']);
        }

        return $this;
    }
}

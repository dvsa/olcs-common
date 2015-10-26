<?php

/**
 * Renders a date time select element
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormDateTimeSelect as ZendFormDateTimeSelect;
use DateTime;
use IntlDateFormatter;
use Zend\Form\Element\DateTimeSelect as DateTimeSelectElement;
use Zend\Form\Exception;
use Zend\Form\View\Helper\FormDateSelect as FormDateSelectHelper;

/**
 * Renders a date time select element
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class FormDateTimeSelect extends ZendFormDateTimeSelect
{
    private $inputHelper;

    private $format = '<div class="field inline-text"><label>%s</label>%s</div>';

    /**
     * Render a date element that is composed of six selects
     *
     * @param  ElementInterface $element
     * @return string
     * @throws \Zend\Form\Exception\InvalidArgumentException
     * @throws \Zend\Form\Exception\DomainException
     */
    public function render(ElementInterface $element)
    {
        $this->pattern = $element->getOption('pattern');

        if (!$element instanceof DateTimeSelectElement) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s requires that the element is of type Zend\Form\Element\DateTimeSelect',
                __METHOD__
            ));
        }

        $name = $element->getName();
        if ($name === null || $name === '') {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $shouldRenderDelimiters = $element->shouldRenderDelimiters();
        $selectHelper = $this->getSelectElementHelper();
        $pattern      = $this->parsePattern($shouldRenderDelimiters);

        $hourOptions   = $this->getHoursOptions($pattern['hour']);
        $minuteOptions = $this->getMinutesOptions($pattern['minute']);
        $secondOptions = $this->getSecondsOptions($pattern['second']);

        $dayElement    = $element->getDayElement();
        $monthElement  = $element->getMonthElement();
        $yearElement   = $element->getYearElement();
        $hourElement   = $element->getHourElement()->setValueOptions($hourOptions);
        $minuteElement = $element->getMinuteElement()->setValueOptions($minuteOptions);
        $secondElement = $element->getSecondElement()->setValueOptions($secondOptions);

        if ($element->shouldCreateEmptyOption()) {
            $hourElement->setEmptyOption('');
            $minuteElement->setEmptyOption('');
            $secondElement->setEmptyOption('');
        }

        $data = array();
        $data[$pattern['day']]    = $this->renderDayInput($dayElement);
        $data[$pattern['month']]  = $this->renderMonthInput($monthElement);
        $data[$pattern['year']]   = $this->renderYearInput($yearElement);
        $data[$pattern['hour']]   = $selectHelper->render($hourElement);
        $data[$pattern['minute']] = $selectHelper->render($minuteElement);

        if ($element->shouldShowSeconds()) {
            $data[$pattern['second']]  = $selectHelper->render($secondElement);
        } else {
            unset($pattern['second']);
            if ($shouldRenderDelimiters) {
                unset($pattern[4]);
            }
        }

        $markup = '';
        foreach ($pattern as $key => $value) {
            // Delimiter
            if (is_numeric($key)) {
                $markup .= $value;
            } else {
                $markup .= $data[$value];
            }
        }

        return trim($markup);
    }

    protected function renderDayInput($element)
    {
        return $this->wrap(
            $this->renderInput($element, 2),
            'Day'
        );
    }

    protected function renderMonthInput($element)
    {
        return $this->wrap(
            $this->renderInput($element, 2),
            'Month'
        );
    }

    protected function renderYearInput($element)
    {
        return $this->wrap(
            $this->renderInput($element, 4),
            'Year'
        );
    }

    protected function wrap($content, $label)
    {
        $label = $this->getTranslator()->translate('date-' . $label);

        return sprintf($this->format, $label, $content);
    }

    protected function renderInput($element, $maxLength)
    {
        $inputHelper = $this->getInputHelper();

        $element->setAttribute('maxlength', $maxLength);

        return $inputHelper->render($element);
    }

    protected function getInputHelper()
    {
        if ($this->inputHelper === null) {
            $this->inputHelper = $this->view->plugin('forminput');
        }

        return $this->inputHelper;
    }

    /**
     * Create a key => value options for minutes
     *
     * @param  string $pattern Pattern to use for minutes
     * @return array
     */
    protected function getMinutesOptions($pattern)
    {
        $keyFormatter   = new IntlDateFormatter($this->getLocale(), null, null, null, null, 'mm');
        $valueFormatter = new IntlDateFormatter($this->getLocale(), null, null, null, null, $pattern);
        $date           = new DateTime('1970-01-01 00:00:00');

        $result = array();
        for ($min = 1; $min <= 4; $min++) {
            $key   = $keyFormatter->format($date);
            $value = $valueFormatter->format($date);
            $result[$key] = $value;

            $date->modify('+15 minute');
        }

        return $result;
    }
}

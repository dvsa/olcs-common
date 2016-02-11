<?php

/**
 * Form Date Select
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\View\Helper;

use Zend\Form\View\Helper\FormDateSelect as ZendFormDateSelect;
use Zend\Form\ElementInterface;
use Zend\Form\Element\DateSelect as DateSelectElement;
use Zend\Form\Exception;
use Zend\Form\View\Helper\FormMonthSelect as FormMonthSelectHelper;

/**
 * Form Date Select
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormDateSelect extends \Common\Form\View\Helper\Extended\FormDateSelect
{
    private $inputHelper;

    private $format = '<div class="field inline-text"><label>%s</label>%s</div>';

    /**
     * Render a date element that is composed of three selects
     *
     * @param  ElementInterface $element
     * @throws \Zend\Form\Exception\InvalidArgumentException
     * @throws \Zend\Form\Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element)
    {
        if (!$element instanceof DateSelectElement) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    '%s requires that the element is of type Zend\Form\Element\DateSelect',
                    __METHOD__
                )
            );
        }

        $name = $element->getName();
        if ($name === null || $name === '') {
            throw new Exception\DomainException(
                sprintf(
                    '%s requires that the element has an assigned name; none discovered',
                    __METHOD__
                )
            );
        }

        $pattern      = $this->parsePattern($element->shouldRenderDelimiters());

        $dayElement   = $element->getDayElement();
        $monthElement = $element->getMonthElement();
        $yearElement  = $element->getYearElement();

        $data = array();
        $data[$pattern['day']]   = $this->renderDayInput($dayElement);
        $data[$pattern['month']] = $this->renderMonthInput($monthElement);
        $data[$pattern['year']]  = $this->renderYearInput($yearElement);

        $markup = '';
        foreach ($pattern as $key => $value) {
            // Delimiter
            if (is_numeric($key)) {
                $markup .= $value;
            } else {
                $markup .= $data[$value];
            }
        }

        return $markup;
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
}

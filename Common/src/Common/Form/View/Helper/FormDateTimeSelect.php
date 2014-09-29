<?php
/**
 * Renders a date time select element
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */

namespace Common\Form\View\Helper;

use Zend\Form\Exception;
use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormDateTimeSelect as ZendFormDateTimeSelect;
use Common\Form\Elements\Custom\DateTimeSelect as DateTimeSelectElement;
use IntlDateFormatter;
use DateTime;

/**
 * Renders a date time select element
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class FormDateTimeSelect extends ZendFormDateTimeSelect
{
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
        return parent::render($element);
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

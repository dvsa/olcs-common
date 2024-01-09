<?php

namespace Common\Form\View\Helper\Readonly;

use Common\Module;
use Laminas\Form\Element\DateSelect;
use Laminas\Form\ElementInterface;
use Laminas\View\Helper\AbstractHelper;

/**
 * Class FormDateSelect
 * @package Common\Form\View\Helper\Readonly
 */
class FormDateSelect extends AbstractHelper
{
    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface|null $element
     * @return string|FormElement
     */
    public function __invoke(ElementInterface $element = null)
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element);
    }

    /**
     * @param ElementInterface $element
     * @return string
     */
    public function render(ElementInterface $element)
    {
        if (!($element instanceof DateSelect)) {
            return '';
        }

        if (empty($element->getYearElement()->getValue())
            || empty($element->getMonthElement()->getValue())
            || empty($element->getDayElement()->getValue())
        ) {
            return '';
        }

        $dateTime = new \DateTime();
        $dateTime->setDate(
            (int) $element->getYearElement()->getValue(),
            (int) $element->getMonthElement()->getValue(),
            (int) $element->getDayElement()->getValue()
        );

        return $dateTime->format(Module::$dateFormat);
    }
}

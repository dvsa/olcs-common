<?php

namespace Common\Form\View\Helper\Readonly;

use Zend\Form\Element\DateSelect;
use Zend\Form\ElementInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * Class FormDateSelect
 * @package Common\Form\View\Helper\Readonly
 */
class FormDateSelect extends AbstractHelper
{
    /**
     * @var string
     */
    protected $format = 'd/m/Y';

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

        $dateTime = new \DateTime();
        $dateTime->setDate(
            $element->getYearElement()->getValue(),
            $element->getMonthElement()->getValue(),
            $element->getDayElement()->getValue()
        );
        
        return $dateTime->format($this->format);
    }
}

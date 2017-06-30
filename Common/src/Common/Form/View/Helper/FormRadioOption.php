<?php

namespace Common\Form\View\Helper;

use Zend\Form\ElementInterface;

/**
 * Class FormRadioOption
 *
 * The decision to implement like this was to minimize the amout of code that would need to be copied from Zend Helpers
 *
 * @package Common\Form\View\Helper
 */
class FormRadioOption extends \Common\Form\View\Helper\Extended\FormRadio
{
    /**
     * Invoke helper
     *
     * @param ElementInterface|null $element       Radio element
     * @param mixed                 $labelPosition key of option to render, (strict standards do not allow changing
     *                                             method signature)
     *
     * @return $this|string
     */
    public function __invoke(ElementInterface $element = null, $labelPosition = null)
    {
        if (!$element) {
            return $this;
        }
        $key = $labelPosition;

        // Only want to render one option, so store all options in tmp varaiable
        $savedOptions = $element->getValueOptions();
        $element->setValueOptions([$key => $savedOptions[$key]]);

        $rendered = $this->render($element);

        // put original value options back
        $element->setValueOptions($savedOptions);

        // Change the rendered HTML, by moving the input outside of the label
        preg_match('/<input.*?>/', $rendered, $matches);
        $input = $matches[0];
        $rendered = $input . str_replace($input, '', $rendered);

        return $rendered;
    }
}

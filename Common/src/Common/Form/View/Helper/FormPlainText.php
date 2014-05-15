<?php

/**
 *  PlainText Form View Helper
 *
 *  @author Jakub Igla <jakub.igla@valtech.co.uk>
 */

namespace Common\Form\View\Helper;

use Zend\Form\View\Helper\AbstractHelper;
use Zend\Form\ElementInterface;

/**
 *  PlainText Form View Helper
 *
 *  @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class FormPlainText extends AbstractHelper
{

    /**
     * @param ElementInterface $element
     * @return mixed
     */
    public function __invoke(ElementInterface $element = null)
    {
        return $this->render($element);
    }

    /**
     * @param ElementInterface $element
     * @return mixed
     */
    public function render(ElementInterface $element)
    {
        return '<p class="hint">' . $this->getView()->translate($element->getValue()) . '</p>';
    }

}
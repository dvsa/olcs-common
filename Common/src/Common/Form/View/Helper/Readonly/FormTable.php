<?php

namespace Common\Form\View\Helper\Readonly;

use Common\Form\Elements\Types\Table;
use Zend\Form\ElementInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\EscapeHtml;

/**
 * Class FormTable
 * @package Common\Form\View\Helper\Readonly
 */
class FormTable extends AbstractHelper
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
        if (!($element instanceof Table)) {
            return '';
        }
        return $this->render($element);

        $value = '';

        $escapeHelper = $this->getEscapeHtmlHelper();

        return $escapeHelper($value);
    }
}

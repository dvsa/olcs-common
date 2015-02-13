<?php

namespace Common\Form\View\Helper\Readonly;

use Zend\Form\ElementInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\EscapeHtml;

/**
 * Class FormItem
 * @package Common\Form\View\Helper\Readonly
 */
class FormItem extends AbstractHelper
{
    /**
     * @var EscapeHtml
     */
    protected $escapeHtmlHelper;

    /**
     * Retrieve the escapeHtml helper
     *
     * @return EscapeHtml
     */
    protected function getEscapeHtmlHelper()
    {
        if ($this->escapeHtmlHelper) {
            return $this->escapeHtmlHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->escapeHtmlHelper = $this->view->plugin('escapehtml');
        }

        if (!$this->escapeHtmlHelper instanceof EscapeHtml) {
            $this->escapeHtmlHelper = new EscapeHtml();
        }

        return $this->escapeHtmlHelper;
    }
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
     * @return mixed
     */
    public function render(ElementInterface $element)
    {
        $escapeHelper = $this->getEscapeHtmlHelper();
        return $escapeHelper($element->getValue());
    }
}

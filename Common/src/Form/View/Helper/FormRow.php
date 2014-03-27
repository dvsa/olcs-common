<?php
namespace Common\Form\View\Helper;

use Zend\Form\View\Helper\FormRow as ZendFormRow;
use Zend\Form\ElementInterface as ZendElementInterface;
use Common\Form\View\Helper\Traits as AlphaGovTraits;

class FormRow extends ZendFormRow
{
    use AlphaGovTraits\Logger;

    /**
     * The form row output format.
     *
     * @var string
     */
    private static $format = '<div class="field">%s</div>';
    private static $errorClass = '<div class="validation-wrapper">%s</div>';

    /**
     * Utility form helper that renders a label (if it exists), an element and errors
     *
     * @param  ZendElementInterface $element
     * @throws \Zend\Form\Exception\DomainException
     * @return string
     */
    public function render(ZendElementInterface $element)
    {
        $this->log('Rendering Form Row', LOG_INFO);

        $oldRenderErrors = $this->getRenderErrors();

        if ($oldRenderErrors) {

            /**
             * We don't want the parent class rto render the errors.
             */
            $this->setRenderErrors(false);
            $elementErrors = $this->getElementErrorsHelper()->render($element);
        }

        $markup = parent::render($element);

        $type = $element->getAttribute('type');
        if ($type === 'multi_checkbox' && $type === 'radio') {
            $noWrap = true;
        }

        if ($oldRenderErrors && $elementErrors != '') {
            $markup = $elementErrors . $markup;
        }

        if (!isset($noWrap)) {
            $markup = sprintf(self::$format, $markup);
        }

        if ($oldRenderErrors && $elementErrors != '') {
            $markup = sprintf(self::$errorClass, $markup);
        }

        $this->setRenderErrors($oldRenderErrors);

        return $markup;
    }
}
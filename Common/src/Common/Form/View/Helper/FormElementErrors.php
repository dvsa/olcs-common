<?php
namespace Common\Form\View\Helper;

use Zend\Form\View\Helper\FormElementErrors as ZendFormElementErrors;
use Zend\Form\ElementInterface as ZendElementInterface;
use Common\Form\View\Helper\Traits as AlphaGovTraits;

class FormElementErrors extends ZendFormElementErrors
{
    use AlphaGovTraits\Logger;

    /**
     * Render validation errors for the provided $element
     *
     * @param  ZendElementInterface $element
     * @param  array $attributes
     * @throws Exception\DomainException
     * @return string
     */
    public function render(ZendElementInterface $element, array $attributes = array())
    {
        $this->log('Rendering Element Errors: ' . $element->getName(), LOG_INFO);

        return parent::render($element);
    }
}

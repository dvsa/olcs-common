<?php

/**
 * Render form element errors
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\View\Helper;

use Zend\Form\View\Helper\FormElementErrors as ZendFormElementErrors;
use Common\Form\View\Helper\Traits as AlphaGovTraits;
use Traversable;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;

/**
 * Render form element errors
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormElementErrors extends ZendFormElementErrors
{
    use AlphaGovTraits\Logger;

    /**
     * Render validation errors for the provided $element
     * @NOTE: Pretty much identical to Zends version except we translate the messages here
     *
     * @param  ElementInterface $element
     * @param  array $attributes
     * @throws Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element, array $attributes = array())
    {
        // @todo Do we really need to log this?
        $this->log('Rendering Element Errors: ' . $element->getName(), LOG_INFO);

        $messages = $element->getMessages();

        if (empty($messages)) {
            return '';
        }

        if (!is_array($messages) && !$messages instanceof Traversable) {
            throw new Exception\DomainException(
                sprintf(
                    '%s expects that $element->getMessages() will return an array or Traversable; received "%s"',
                    __METHOD__,
                    (is_object($messages) ? get_class($messages) : gettype($messages))
                )
            );
        }

        // Prepare attributes for opening tag
        $attributes = array_merge($this->attributes, $attributes);
        $attributes = $this->createAttributesString($attributes);
        if (!empty($attributes)) {
            $attributes = ' ' . $attributes;
        }

        $renderer = $this->getView();

        // Flatten message array
        $escapeHtml      = $this->getEscapeHtmlHelper();
        $messagesToPrint = array();
        array_walk_recursive(
            $messages,
            function ($item) use (&$messagesToPrint, $escapeHtml, $renderer) {
                $item = $renderer->translate($item);
                $messagesToPrint[] = $escapeHtml($item);
            }
        );

        if (empty($messagesToPrint)) {
            return '';
        }

        // Generate markup
        $markup  = sprintf($this->getMessageOpenFormat(), $attributes);
        $markup .= implode($this->getMessageSeparatorString(), $messagesToPrint);
        $markup .= $this->getMessageCloseString();

        return $markup;
    }
}

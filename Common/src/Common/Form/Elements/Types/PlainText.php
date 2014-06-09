<?php

/**
 * Plain Text Element
 *
 * @author Someone <someone@valtech.co.uk>
 */
namespace Common\Form\Elements\Types;

use Zend\Form\Element;

/**
 * Plain Text Element
 *
 * @author Someone <someone@valtech.co.uk>
 */
class PlainText extends Element
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'plaintext',
    );

    /**
     * Set options for an element. Accepted options are:
     * - label: label to associate with the element
     * - label_attributes: attributes to use when the label is rendered
     *
     * @param  array|Traversable $options
     * @return Element|ElementInterface
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['value'])) {
            $this->setValue($options['value']);
        }

        return $this;
    }
}

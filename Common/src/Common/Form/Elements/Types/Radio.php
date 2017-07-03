<?php

namespace Common\Form\Elements\Types;

use Zend\Form\Element\Radio as ZendRadio;

/**
 * Radio form element
 */
class Radio extends ZendRadio
{
    private $uniqid;

    /**
     * Initial value options
     *
     * @return void
     */
    public function init()
    {
        $this->uniqid = uniqid();

        parent::init();
    }

    /**
     * Set element name, override to set id
     *
     * @param string $name Name of radio element
     *
     * @return void
     */
    public function setName($name)
    {
        parent::setName($name);

        if (empty($this->getAttribute('id'))) {
            $this->setAttribute('id', $name);
        }
    }

    /**
     * Override parent to set attributes required
     *
     * @param array $options Options
     *
     * @return void
     */
    public function setValueOptions(array $options)
    {
        foreach ($options as $key => &$optionSpec) {
            if (is_scalar($optionSpec)) {
                $optionSpec = array(
                    'label' => $optionSpec,
                    'value' => $key
                );
            }

            $id = $this->uniqid .'_'. $optionSpec['value'];
            $optionSpec['attributes'] = [
                'id' => $id,
                'class' => 'radio-button',
                'data-show-element' => "#${id}_content",
            ];
            $optionSpec['label_attributes'] = [
                'for' => $id,
                'class' => 'radio-button__label',
            ];
        }

        parent::setValueOptions($options);
    }
}

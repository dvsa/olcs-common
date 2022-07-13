<?php

namespace Common\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\Form\Element\Button;

/**
 * ReadOnly Actions view helper
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ReadOnlyActions extends AbstractHelper
{
    const SECONDARY_CLASS = 'action--secondary';
    const WRAPPER = '<div class="actions-container">%s</div>';
    const LINK_WRAPPER = '<a href="%s" class="%s" %s>%s</a>';
    const ATTRIBUTES = '%s="%s"';

    /**
     * Return an actions for the read only header
     *
     * @return string
     */
    public function __invoke($actions)
    {
        $markup = '';
        foreach ($actions as $action) {
            $class = (isset($action['class']) ? $action['class'] : self::SECONDARY_CLASS);
            if (isset($action['url'])) {
                $attributeString = ' ';

                if (isset($action['attributes']) && is_array($action['attributes'])) {
                    $attributes = [];
                    foreach ($action['attributes'] as $actionKey => $actionVal) {
                        $attributes[] = sprintf(self::ATTRIBUTES, $actionKey, $actionVal);
                    }
                    $attributeString = implode(' ', $attributes);
                }

                $markup .= sprintf(
                    self::LINK_WRAPPER,
                    $action['url'],
                    $class,
                    $attributeString,
                    $this->view->translate($action['label'])
                );
            } else {
                $lowerLabel = strtolower($action['label']);
                $element = new Button(str_replace(' ', '-', $lowerLabel));
                $element->setAttributes(
                    [
                        'type'  => 'submit',
                        'name'  => 'action',
                        'id'    => $lowerLabel,
                        'class' => $class,
                        'value' => $this->view->translate($action['label'])
                    ]
                );

                $markup .= $this->view->formInput($element);
            }
        }

        return sprintf(self::WRAPPER, $markup);
    }
}

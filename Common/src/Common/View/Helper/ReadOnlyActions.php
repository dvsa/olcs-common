<?php

namespace Common\View\Helper;

use Zend\I18n\View\Helper\Translate;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Element\Button;

/**
 * ReadOnly Actions view helper
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ReadOnlyActions extends AbstractHelper implements FactoryInterface
{
    const SECONDARY_CLASS = 'action--secondary';
    const WRAPPER = '<div class="actions-container">%s</div>';
    const LINK_WRAPPER = '<a href="%s" class="%s" %s>%s</a>';
    const ATTRIBUTES = '%s="%s"';

    /**
     * @var Translate
     */
    private $translator;

    /**
     * Inject services
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->translator = $serviceLocator->get('translate');

        return $this;
    }

    /**
     * Return an actions for the read only header
     *
     * @return string
     */
    public function __invoke($actions)
    {
        $translate = $this->translator;
        $markup = '';
        foreach ($actions as $action) {
            if (isset($action['url'])) {

                $attributeString = ' ';

                if (isset($action['attributes']) && is_array($action['attributes'])) {
                    foreach ($action['attributes'] as $actionKey => $actionVal) {
                        $attributeString = sprintf(self::ATTRIBUTES, $actionKey, $actionVal);
                    }
                }

                $markup .= sprintf(
                    self::LINK_WRAPPER,
                    $action['url'],
                    isset($action['class']) ? $action['class'] : self::SECONDARY_CLASS,
                    $attributeString,
                    $translate($action['label'])
                );

            } else {

                $lowerLabel = strtolower($action['label']);
                $element = new Button(str_replace(' ', '-', $lowerLabel));
                $element->setAttribute('type', 'submit');
                $element->setAttribute('name', 'action');
                $element->setAttribute('id', $lowerLabel);
                $element->setAttribute('class', (isset($action['class']) ? $action['class'] : self::SECONDARY_CLASS));
                $element->setAttribute('value', $translate($action['label']));

                $markup .= $this->view->formInput($element);

            }
        }

        return sprintf(self::WRAPPER, $markup);
    }
}

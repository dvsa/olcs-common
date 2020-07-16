<?php

namespace Common\Service\Qa\Custom\Common;

use Common\Form\Elements\Types\Html;
use Zend\Form\Fieldset;
use Zend\View\Helper\Partial;

class WarningAdder
{
    const DEFAULT_PRIORITY = 10;

    /** @var Partial */
    private $partial;

    /**
     * Create service instance
     *
     * @param Partial $partial
     *
     * @return WarningAdder
     */
    public function __construct(Partial $partial)
    {
        $this->partial = $partial;
    }

    /**
     * Add a warning partial to the fieldset
     *
     * @param Fieldset $fieldset
     * @param string $translationKey
     * @param int $priority
     * @param string $elementName
     */
    public function add(
        Fieldset $fieldset,
        $translationKey,
        $priority = self::DEFAULT_PRIORITY,
        $elementName = 'warning'
    ) {
        $markup = $this->partial->__invoke(
            'partials/warning-component',
            ['translationKey' => $translationKey]
        );

        $fieldset->add(
            [
                'name' => $elementName,
                'type' => Html::class,
                'attributes' => [
                    'value' => $markup
                ]
            ],
            [
                'priority' => $priority
            ]
        );
    }
}

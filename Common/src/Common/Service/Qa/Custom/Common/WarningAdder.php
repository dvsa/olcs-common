<?php

namespace Common\Service\Qa\Custom\Common;

use Zend\Form\Fieldset;
use Zend\View\Helper\Partial;

class WarningAdder
{
    const DEFAULT_PRIORITY = 10;

    /** @var Partial */
    private $partial;

    /** @var HtmlAdder */
    private $htmlAdder;

    /**
     * Create service instance
     *
     * @param Partial $partial
     * @param HtmlAdder $htmlAdder
     *
     * @return WarningAdder
     */
    public function __construct(Partial $partial, HtmlAdder $htmlAdder)
    {
        $this->partial = $partial;
        $this->htmlAdder = $htmlAdder;
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

        $this->htmlAdder->add($fieldset, $elementName, $markup, $priority);
    }
}

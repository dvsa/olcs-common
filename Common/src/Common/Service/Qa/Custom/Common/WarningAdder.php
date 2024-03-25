<?php

namespace Common\Service\Qa\Custom\Common;

use Laminas\Form\Fieldset;
use Laminas\View\Helper\Partial;

class WarningAdder
{
    public const DEFAULT_PRIORITY = 10;

    /** @var Partial */
    private $partial;

    /** @var HtmlAdder */
    private $htmlAdder;

    /**
     * Create service instance
     *
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
     * @param string $translationKey
     * @param int $priority
     * @param string $elementName
     */
    public function add(
        Fieldset $fieldset,
        $translationKey,
        $priority = self::DEFAULT_PRIORITY,
        $elementName = 'warning'
    ): void {
        $markup = $this->partial->__invoke(
            'partials/warning-component',
            ['translationKey' => $translationKey]
        );

        $this->htmlAdder->add($fieldset, $elementName, $markup, $priority);
    }
}

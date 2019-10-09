<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\Elements\Types\Html;
use Zend\Form\Fieldset;
use Zend\View\Helper\Partial;

class NiWarningConditionalAdder
{
    /** @var Partial */
    private $partial;

    /**
     * Create service instance
     *
     * @param Partial $partial
     *
     * @return NiWarningConditionalAdder
     */
    public function __construct(Partial $partial)
    {
        $this->partial = $partial;
    }

    /**
     * Add the NI warning to the fieldset if showNiWarning is true
     *
     * @param Fieldset $fieldset
     * @param bool $showNiWarning
     */
    public function addIfRequired(Fieldset $fieldset, $showNiWarning)
    {
        if ($showNiWarning) {
            $markup = $this->partial->__invoke(
                'partials/warning-component',
                ['translationKey' => 'permits.page.number-of-trips.northern-ireland.warning']
            );

            $fieldset->add(
                [
                    'name' => 'niWarning',
                    'type' => Html::class,
                    'attributes' => [
                        'value' => $markup
                    ]
                ]
            );
        }
    }
}

<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\Custom\Common\HtmlAdder;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Common\Service\Qa\TextFieldsetPopulator;
use Zend\Form\Element\Hidden;
use Zend\Form\Fieldset;

class AnnualTripsAbroadFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var TextFieldsetPopulator */
    private $textFieldsetPopulator;

    /** @var TranslationHelperService */
    private $translator;

    /** @var NiWarningConditionalAdder */
    private $niWarningConditionalAdder;

    /** @var HtmlAdder */
    private $htmlAdder;

    /**
     * Create service instance
     *
     * @param TextFieldsetPopulator $textFieldsetPopulator
     * @param TranslationHelperService $translator
     * @param NiWarningConditionalAdder $niWarningConditionalAdder
     * @param HtmlAdder $htmlAdder
     *
     * @return AnnualTripsAbroadFieldsetPopulator
     */
    public function __construct(
        TextFieldsetPopulator $textFieldsetPopulator,
        TranslationHelperService $translator,
        NiWarningConditionalAdder $niWarningConditionalAdder,
        HtmlAdder $htmlAdder
    ) {
        $this->textFieldsetPopulator = $textFieldsetPopulator;
        $this->translator = $translator;
        $this->niWarningConditionalAdder = $niWarningConditionalAdder;
        $this->htmlAdder = $htmlAdder;
    }

    /**
     * Populate the fieldset with elements based on the supplied options array
     *
     * @param mixed $form
     * @param Fieldset $fieldset
     * @param array $options
     */
    public function populate($form, Fieldset $fieldset, array $options)
    {
        $this->niWarningConditionalAdder->addIfRequired($fieldset, $options['showNiWarning']);

        $guidanceBlueMarkup = sprintf(
            '<div class="guidance-blue">%s</div>',
            $this->translator->translate('qanda.ecmt.annual-trips-abroad.guidance')
        );

        $ecmtTripsHintMarkup = $this->translator->translate('markup-ecmt-trips-hint');

        $this->htmlAdder->add($fieldset, 'hint', $guidanceBlueMarkup . $ecmtTripsHintMarkup);

        $fieldset->add(
            [
                'name' => 'warningVisible',
                'type' => Hidden::class,
                'attributes' => [
                    'value' => 0
                ]
            ]
        );

        $this->textFieldsetPopulator->populate($form, $fieldset, $options['text']);
    }
}

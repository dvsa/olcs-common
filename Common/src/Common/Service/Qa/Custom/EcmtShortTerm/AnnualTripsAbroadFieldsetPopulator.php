<?php

namespace Common\Service\Qa\Custom\EcmtShortTerm;

use Common\Form\Elements\Types\Html;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Common\Service\Qa\TextFieldsetPopulator;
use Zend\Form\Fieldset;

class AnnualTripsAbroadFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var TextFieldsetPopulator */
    private $textFieldsetPopulator;

    /** @var TranslationHelperService */
    private $translator;

    /**
     * Create service instance
     *
     * @param TextFieldsetPopulator $textFieldsetPopulator
     * @param TranslationHelperService $translator
     *
     * @return AnnualTripsAbroadFieldsetPopulator
     */
    public function __construct(
        TextFieldsetPopulator $textFieldsetPopulator,
        TranslationHelperService $translator
    ) {
        $this->textFieldsetPopulator = $textFieldsetPopulator;
        $this->translator = $translator;
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
        $markup = $this->translator->translate('markup-ecmt-trips-hint');

        $fieldset->add(
            [
                'name' => 'hint',
                'type' => Html::class,
                'attributes' => [
                    'value' => $markup
                ]
            ]
        );

        $this->textFieldsetPopulator->populate($form, $fieldset, $options);
    }
}

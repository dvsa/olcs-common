<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Common\Service\Qa\RadioFieldsetPopulator;
use Laminas\Form\Fieldset;

class SectorsFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var TranslationHelperService */
    private $translator;

    /** @var RadioFieldsetPopulator */
    private $radioFieldsetPopulator;

    /**
     * Create service instance
     *
     * @param TranslationHelperService $translator
     * @param RadioFieldsetPopulator $radioFieldsetPopulator
     *
     * @return SectorsFieldsetPopulator
     */
    public function __construct(TranslationHelperService $translator, RadioFieldsetPopulator $radioFieldsetPopulator)
    {
        $this->translator = $translator;
        $this->radioFieldsetPopulator = $radioFieldsetPopulator;
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
        $this->radioFieldsetPopulator->populate($form, $fieldset, $options);

        $qaElement = $fieldset->get('qaElement');
        $valueOptions = $qaElement->getValueOptions();

        $markupBefore = sprintf(
            '<div class="govuk-radios__divider">%s</div>',
            $this->translator->translate('qanda.ecmt.sectors.divider.or')
        );

        // we assume that the markup needs to be added before the final radio button
        $valueOptions[count($valueOptions) - 1]['markup_before'] = $markupBefore;

        $qaElement->setValueOptions($valueOptions);
    }
}

<?php

namespace Common\Service\Qa;

use Zend\Form\Fieldset;

class RadioOrHtmlFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var RadioFieldsetPopulator */
    private $radioFieldsetPopulator;

    /** @var HtmlFieldsetPopulator */
    private $htmlFieldsetPopulator;

    /**
     * Create service instance
     *
     * @param RadioFieldsetPopulator $radioFieldsetPopulator
     * @param HtmlFieldsetPopulator $htmlFieldsetPopulator
     *
     * @return RadioOrHtmlFieldsetPopulator
     */
    public function __construct(
        RadioFieldsetPopulator $radioFieldsetPopulator,
        HtmlFieldsetPopulator $htmlFieldsetPopulator
    ) {
        $this->radioFieldsetPopulator = $radioFieldsetPopulator;
        $this->htmlFieldsetPopulator = $htmlFieldsetPopulator;
    }

    /**
     * Populate the fieldset with a radio or html element based on the supplied options array
     *
     * @param mixed $form
     * @param Fieldset $fieldset
     * @param array $options
     */
    public function populate($form, Fieldset $fieldset, array $options)
    {
        if (count($options['options']) === 1) {
            // display html for single value
            $this->htmlFieldsetPopulator->populate($form, $fieldset, $options);

            // change Submit label
            $form->get('Submit')->get('SubmitButton')->setValue('permits.button.continue');
        } else {
            // otherwise standard radio
            $this->radioFieldsetPopulator->populate($form, $fieldset, $options);
        }
    }
}

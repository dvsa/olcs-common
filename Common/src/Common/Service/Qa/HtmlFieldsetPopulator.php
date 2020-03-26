<?php

namespace Common\Service\Qa;

use Common\Form\Elements\Types\Html;
use Common\Service\Helper\TranslationHelperService;
use Zend\Form\Element\Hidden;
use Zend\Form\Fieldset;

class HtmlFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var TranslationHelperService */
    private $translationHelperService;

    /**
     * Create service instance
     *
     * @param TranslationHelperService $translationHelperService
     *
     * @return HtmlFieldsetPopulator
     */
    public function __construct(
        TranslationHelperService $translationHelperService
    ) {
        $this->translationHelperService = $translationHelperService;
    }

    /**
     * Populate the fieldset with an html and hidden element based on the supplied options array
     *
     * @param mixed $form
     * @param Fieldset $fieldset
     * @param array $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function populate($form, Fieldset $fieldset, array $options)
    {
        $firstOption = $options['options'][0];

        // add html field
        $fieldset->add(
            [
                'name' => 'qaHtml',
                'type' => Html::class,
                'attributes' => [
                    'value' => $this->translationHelperService->translate($firstOption['label']),
                ]
            ]
        );

        // add hidden field
        $fieldset->add(
            [
                'name' => 'qaElement',
                'type' => Hidden::class,
                'attributes' => [
                    'value' => $firstOption['value'],
                ]
            ]
        );
    }
}

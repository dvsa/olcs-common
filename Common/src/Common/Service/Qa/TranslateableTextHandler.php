<?php

namespace Common\Service\Qa;

use Common\Service\Helper\TranslationHelperService;

class TranslateableTextHandler
{
    /** @var FormattedTranslateableTextParametersGenerator */
    private $formattedTranslateableTextParametersGenerator;

    /** @var TranslationHelperService */
    private $translationHelper;

    /**
     * Create service instance
     *
     * @param FormattedTranslateableTextParametersGenerator $formattedTranslateableTextParametersGenerator
     * @param TranslationHelperService $translationHelper
     *
     * @return TranslateableTextHandler
     */
    public function __construct(
        FormattedTranslateableTextParametersGenerator $formattedTranslateableTextParametersGenerator,
        TranslationHelperService $translationHelper
    ) {
        $this->formattedTranslateableTextParametersGenerator = $formattedTranslateableTextParametersGenerator;
        $this->translationHelper = $translationHelper;
    }

    /**
     * Derive a translated string from a translatable text array representation
     *
     * @param array $translateableText
     *
     * @return string
     */
    public function translate(array $translateableText)
    {
        return $this->translationHelper->translateReplace(
            $translateableText['key'],
            $this->formattedTranslateableTextParametersGenerator->generate($translateableText['parameters'])
        );
    }
}

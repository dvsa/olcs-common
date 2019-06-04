<?php

namespace Common\Service\Qa;

use Common\Service\Helper\TranslationHelperService;

class TranslateableTextHandler
{
    /** @var TranslationHelperService */
    private $translationHelper;

    /**
     * Create service instance
     *
     * @param TranslationHelperService $translationHelper
     *
     * @return TranslateableTextHandler
     */
    public function __construct(TranslationHelperService $translationHelper)
    {
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
            $translateableText['parameters']
        );
    }
}

<?php

namespace Common\Service\Qa;

use Common\Service\Helper\TranslationHelperService;

class FormattedTranslateableTextParametersGenerator
{
    /** @var TranslateableTextParameterHandler */
    private $translateableTextParameterHandler;

    /**
     * Create service instance
     *
     * @param TranslateableTextParameterHandler $translateableTextParameterHandler
     *
     * @return FormattedTranslateableTextParametersGenerator
     */
    public function __construct(TranslateableTextParameterHandler $translateableTextParameterHandler)
    {
        $this->translateableTextParameterHandler = $translateableTextParameterHandler;
    }

    public function generate(array $parameters)
    {
        $formattedParameters = [];
        foreach ($parameters as $parameter) {
            $formattedParameters[] = $this->translateableTextParameterHandler->handle($parameter);
        }

        return $formattedParameters;
    }
}

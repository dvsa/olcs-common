<?php

namespace Common\Service\Qa\Custom\Ecmt;

use Common\Service\Helper\TranslationHelperService;
use Common\View\Helper\CurrencyFormatter;

class NoOfPermitsBaseInsetTextGenerator
{
    /** @var TranslationHelperService */
    private $translator;

    /** @var CurrencyFormatter */
    private $currencyFormatter;

    /**
     * Create service instance
     *
     * @param TranslationHelperService $translator
     * @param CurrencyFormatter $currencyFormatter
     *
     * @return NoOfPermitsBaseInsetTextGenerator
     */
    public function __construct(TranslationHelperService $translator, CurrencyFormatter $currencyFormatter)
    {
        $this->translator = $translator;
        $this->currencyFormatter = $currencyFormatter;
    }

    /**
     * Generate the base inset text for use on the number of permits page
     *
     * @param array $options
     *
     * @return string
     */
    public function generate(array $options)
    {
        return sprintf(
            $this->translator->translate('qanda.ecmt.number-of-permits.inset.base'),
            $this->currencyFormatter->__invoke($options['applicationFee']),
            $this->currencyFormatter->__invoke($options['issueFee'])
        );
    }
}

<?php

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Util\Escape;
use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;

/**
 *
 * @package Common\Service\Table\Formatter
 *
*
 */
class SearchBusRegSelfserve implements FormatterPluginManagerInterface
{
    private UrlHelperService $urlHelper;
    private TranslatorDelegator $translator;

    /**
     * @param UrlHelperService $urlHelper
     */
    public function __construct(UrlHelperService $urlHelper, TranslatorDelegator $translator)
    {
        $this->urlHelper = $urlHelper;
        $this->translator = $translator;
    }

    /**
     *
     * @param array $data The row data.
     * @param array $column The column data.
     *
     * @return string
     */
    public function format($data, $column = [])
    {
        $url = $this->urlHelper->fromRoute(
            'search-bus/details',
            ['busRegId' => $data['busregId']]
        );

        return sprintf(
            '<a class="govuk-link" href="%s">%s</a><br/>%s',
            $url,
            Escape::html($data['regNo']),
            $this->translator->translate($data['busRegStatus'])
        );
    }
}

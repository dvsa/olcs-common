<?php

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;

/**
 *
 * @package Common\Service\Table\Formatter
 *
*
 */
class SearchApplicationLicenceNo implements FormatterPluginManagerInterface
{
    protected UrlHelperService $urlHelper;

    /**
     * @param UrlHelperService    $urlHelper
     */
    public function __construct(UrlHelperService $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    /**
     *
     * @param array $data   The row data.
     * @param array $column The column data.
     *
     * @return string
     */
    public function format($data, $column = [])
    {
        $url = $this->urlHelper->fromRoute('licence', ['licence' => $data['licId']]);
        return '<a class="govuk-link" href="' . $url . '">' . $data['licNo'] . '</a>';
    }
}

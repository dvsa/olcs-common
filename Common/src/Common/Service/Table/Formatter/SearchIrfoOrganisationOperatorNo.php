<?php

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;

/**
 *
 * @package Common\Service\Table\Formatter
 *
*
 */
class SearchIrfoOrganisationOperatorNo implements FormatterPluginManagerInterface
{
    protected UrlHelperService $urlHelper;

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
        $url = $this->urlHelper->fromRoute(
            'operator/business-details',
            ['organisation' => $data['orgId']]
        );
        return sprintf('<a class="govuk-link" href="%s">%d</a>', $url, $data['orgId']);
    }
}

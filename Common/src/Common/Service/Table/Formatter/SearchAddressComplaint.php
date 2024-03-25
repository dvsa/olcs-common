<?php

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Util\Escape;

/**
 *
 * @package Common\Service\Table\Formatter
 *
*
 */
class SearchAddressComplaint implements FormatterPluginManagerInterface
{
    protected UrlHelperService $urlHelper;

    public function __construct(UrlHelperService $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    /**
     * @param array $data   The row data.
     * @param array $column The column data.
     *
     * @return string The document link and accessed indicator
     */
    public function format($data, $column = [])
    {
        if ($data['complaint'] === 'Yes') {
            return sprintf(
                '<a class="govuk-link" href="%s">Yes</a>',
                $this->urlHelper->fromRoute('licence/opposition', ['licence' => $data['licId']])
            );
        }

        return 'No';
    }
}

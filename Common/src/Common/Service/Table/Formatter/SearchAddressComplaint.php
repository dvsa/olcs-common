<?php

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Util\Escape;

/**
 * Class AccessedCorrespondence
 *
 * Accessed correspondence formatter, displays correspondence as a link to the document and
 * denotes whether the correspondence has been accessed.
 *
 * @package Common\Service\Table\Formatter
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class SearchAddressComplaint implements FormatterPluginManagerInterface
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
     * Get a link for the document with the access indicator.
     *
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
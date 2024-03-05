<?php

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;

/**
 * Class LicenceNumberLink
 *
 * Takes a licence array and creates and outputs a link for that licence.
 *
 * @package Common\Service\Table\Formatter
 */
class InternalLicenceNumberLink implements FormatterPluginManagerInterface
{
    private UrlHelperService $urlHelper;

    /**
     * @param UrlHelperService $urlHelper
     */
    public function __construct(UrlHelperService $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }
    /**
     * Return a the licence URL in a link format for a table.
     *
     * @param array $data   The row data.
     * @param array $column The column
     *
     * @return string
     */
    public function format($data, $column = [])
    {
        $licenceNo = $data['licence']['licNo'];
        $url = $this->urlHelper->fromRoute('lva-licence', ['licence' => $data['licence']['id']]);

        return '<a class="govuk-link" href="' . $url . '" title="Licence details for ' . $licenceNo . '">' . $licenceNo . '</a>';
    }
}

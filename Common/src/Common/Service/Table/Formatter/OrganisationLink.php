<?php

/**
 * OrganisationLink.php
 */

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Util\Escape;

/**
 * Class OrganisationLink
 *
 * Takes a organisation array and creates and outputs a link for that organisation.
 *
 * @package Common\Service\Table\Formatter
 */
class OrganisationLink implements FormatterPluginManagerInterface
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
     * Return a the organisation URL in a link format for a table.
     *
     * @param      array $data   The row data.
     * @param      array $column The column
     * @inheritdoc
     *
     * @return string
     */
    public function format($data, $column = [])
    {
        $url = $this->urlHelper->fromRoute('operator/business-details', ['organisation' => $data['organisation']['id']]);
        return '<a class="govuk-link" href="' . $url . '">' . Escape::html($data['organisation']['name']) . '</a>';
    }
}

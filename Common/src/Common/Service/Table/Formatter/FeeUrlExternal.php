<?php

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Util\Escape;
use Laminas\Http\Request;
use Laminas\Router\Http\TreeRouteStack;

/**
 * External fee url
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class FeeUrlExternal extends FeeUrl
{
    private Request $request;

    private UrlHelperService $urlHelper;

    public function __construct(TreeRouteStack $router, Request $request, UrlHelperService $urlHelper)
    {
        $this->request = $request;
        $this->urlHelper = $urlHelper;
        parent::__construct($router, $request, $urlHelper);
    }

    /**
     * Format a fee amount
     *
     * @param array $row    row
     * @param array $column column
     *
     * @return string
     */
    public function format($row, $column = [])
    {
        if (isset($row['isExpiredForLicence']) && $row['isExpiredForLicence']) {
            $query = $this->request->getQuery()->toArray();
            $url = $this->urlHelper->fromRoute('fees/late', ['fee' => $row['id']], ['query' => $query], true);
            return '<a class="govuk-link" href="' . $url . '">' . Escape::html($row['description']) . '</a>';
        }

        return parent::format($row, $column);
    }
}

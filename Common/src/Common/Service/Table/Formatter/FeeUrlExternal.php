<?php

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;

/**
 * External fee url
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class FeeUrlExternal extends FeeUrl
{
    /**
     * Format a fee amount
     *
     * @param array                               $row    row
     * @param array                               $column column
     * @param \Laminas\ServiceManager\ServiceManager $sm     service locator
     *
     * @return string
     */
    public static function format($row, $column = [], $serviceLocator = null)
    {
        if (isset($row['isExpiredForLicence']) && $row['isExpiredForLicence']) {
            $query      = $serviceLocator->get('request')->getQuery()->toArray();
            $urlHelper  = $serviceLocator->get('Helper\Url');
            $url = $urlHelper->fromRoute('fees/late', ['fee' => $row['id']], ['query' => $query], true);
            return '<a class="govuk-link" href="'. $url . '">'. Escape::html($row['description']) . '</a>';
        }
        return parent::format($row, $column, $serviceLocator);
    }
}

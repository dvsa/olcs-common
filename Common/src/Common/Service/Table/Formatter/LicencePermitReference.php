<?php

/**
 * Status formatter
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;

/**
 * Status formatter
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
class LicencePermitReference implements FormatterInterface
{
    /**
     * status
     *
     * @param array                               $row            Row data
     * @param array                               $column         Column data
     * @param \Zend\ServiceManager\ServiceManager $serviceLocator Service locator
     *
     * @return string
     * @inheritdoc
     */
    public static function format($row, $column = null, $serviceLocator = null)
    {
        $urlHelper = $serviceLocator->get('Helper\Url');
        $route = 'application-overview';

        if ($row['isValid']) {
            $route = 'ecmt-valid-permits';
        } elseif ($row['isFeePaid'] || $row['isIssueInProgress']) {
            $route = null;
        } elseif ($row['isAwaitingFee']) {
            $route = 'ecmt-awaiting-fee';
        } elseif ($row['isUnderConsideration']) {
            $route = 'ecmt-under-consideration';
        }

        return isset($route)
            ? vsprintf(
                '<a class="overview__link" href="%s"><span class="overview__link--underline">%s</span></a>',
                [
                    $urlHelper->fromRoute('permits/' . $route, ['id' => $row['id']]),
                    Escape::html($row['applicationRef'])
                ]
            )
            : Escape::html($row['applicationRef']);
    }
}

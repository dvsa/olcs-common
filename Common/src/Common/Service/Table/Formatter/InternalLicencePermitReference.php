<?php

/**
 * Internal licence permit reference formatter
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */

namespace Common\Service\Table\Formatter;

use Common\RefData;
use Common\Util\Escape;

/**
 * Internal licence permit reference formatter
 */
class InternalLicencePermitReference implements FormatterInterface
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
        // find a route for the type
        switch ($row['typeId']) {
            case RefData::ECMT_PERMIT_TYPE_ID:
                $route = 'licence/permits/application';
                $params = [
                    'licence' => $row['licenceId'],
                    'action' => 'edit',
                    'permitid' => $row['id']
                ];
                break;
            case RefData::ECMT_SHORT_TERM_PERMIT_TYPE_ID:
            case RefData::ECMT_REMOVAL_PERMIT_TYPE_ID:
            case RefData::IRHP_BILATERAL_PERMIT_TYPE_ID:
            case RefData::IRHP_MULTILATERAL_PERMIT_TYPE_ID:
            case RefData::CERT_ROADWORTHINESS_VEHICLE_PERMIT_TYPE_ID:
            case RefData::CERT_ROADWORTHINESS_TRAILER_PERMIT_TYPE_ID:
                $route = 'licence/irhp-application/application';
                $params = [
                    'licence' => $row['licenceId'],
                    'action' => 'edit',
                    'irhpAppId' => $row['id']
                ];
                break;
        }

        return isset($route)
            ? vsprintf(
                '<a href="%s">%s</a>',
                [
                    $serviceLocator->get('Helper\Url')->fromRoute($route, $params),
                    Escape::html($row['applicationRef'])
                ]
            )
            : Escape::html($row['applicationRef']);
    }
}

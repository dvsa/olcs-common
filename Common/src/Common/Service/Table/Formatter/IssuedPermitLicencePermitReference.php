<?php

/**
 * Issued permit licence permit reference formatter
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
namespace Common\Service\Table\Formatter;

use Common\Util\Escape;
use Common\RefData;
use Zend\ServiceManager\ServiceManager;

/**
 * Issued permit licence permit reference formatter
 */
class IssuedPermitLicencePermitReference implements FormatterInterface
{
    /**
     * Issued permit licence permit reference
     *
     * @param array $row Row data
     * @param array $column Column data
     * @param ServiceManager $serviceLocator Service locator
     *
     * @return string
     */
    public static function format($row, $column = null, $serviceLocator = null)
    {
        $route = 'licence/irhp-application/irhp-permits';
        $params = [
            'licence' => $row['licenceId'],
            'irhpAppId' => $row['id'],
            'permitTypeId' => $row['typeId']
        ];

        $appLinkPermitTypeIds = [
            RefData::CERT_ROADWORTHINESS_VEHICLE_PERMIT_TYPE_ID,
            RefData::CERT_ROADWORTHINESS_TRAILER_PERMIT_TYPE_ID,
        ];

        $permitTypeId = $row['typeId'];

        if (in_array($permitTypeId, $appLinkPermitTypeIds)) {
            $route = 'licence/irhp-application/application';
            $params = [
                'licence' => $row['licenceId'],
                'action' => 'edit',
                'irhpAppId' => $row['id']
            ];
        }

        $urlHelper = $serviceLocator->get('Helper\Url');
        $url = $urlHelper->fromRoute($route, $params);

        return '<a href="' . $url . '">' . Escape::html($row['applicationRef']) . '</a>';
    }
}

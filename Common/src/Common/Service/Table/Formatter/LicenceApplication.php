<?php

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;
use Common\Service\Helper\UrlHelperService as UrlHelper;
use Common\View\Helper\Status as StatusHelper;
use Laminas\ServiceManager\ServiceManager;

/**
 * LicenceApplication formatter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class LicenceApplication implements FormatterInterface
{
    const LINK_WITH_STATUS = '<a href="%s">%s</a>%s';

    /**
     * Format a cell with links to licence and application
     *
     * @param array          $row            row of data
     * @param array          $column         column
     * @param ServiceManager $serviceLocator service locator
     *
     * @return string
     */
    public static function format($row, $column = null, $serviceLocator = null)
    {
        /**
         * @var UrlHelper    $urlHelper
         * @var StatusHelper $statusHelper
         */
        $urlHelper = $serviceLocator->get('Helper\Url');
        $statusHelper = $serviceLocator->get('ViewHelperManager')->get('status');

        $licenceStatus = [
            'id' => $row['licStatus'],
            'description' => $row['licStatusDesc']
        ];

        $licenceLink = sprintf(
            self::LINK_WITH_STATUS,
            $urlHelper->fromRoute('licence', ['licence' => $row['licId']]),
            Escape::html($row['licNo']),
            $statusHelper->__invoke($licenceStatus)
        );

        if (isset($row['appId'])) {
            $appStatus = [
                'id' => $row['appStatus'],
                'description' => $row['appStatusDesc']
            ];

            $appLink = sprintf(
                self::LINK_WITH_STATUS,
                $urlHelper->fromRoute('lva-application', ['application' => $row['appId']]),
                Escape::html($row['appId']),
                $statusHelper->__invoke($appStatus)
            );

            return $licenceLink . '<br />' . $appLink;
        }

        return $licenceLink;
    }
}

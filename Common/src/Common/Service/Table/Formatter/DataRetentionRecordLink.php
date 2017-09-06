<?php

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;
use Zend\Mvc\Controller\Plugin\Url;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Data Retention Record Link
 */
class DataRetentionRecordLink implements FormatterInterface
{
    const ENTITY_TRANSPORT_MANAGER = 'transport_manager';
    const ENTITY_IRFO_GV_PERMIT = 'irfo_gv_permit';
    const ENTITY_IRFO_PSV_AUTH = 'irfo_psv_auth';
    const ENTITY_ORGANISATION = 'organisation';
    const ENTITY_APPLICATION = 'application';
    const ENTITY_BUS_REG = 'bus_reg';
    const ENTITY_LICENCE = 'licence';
    const ENTITY_CASES = 'cases';

    /** @var ServiceLocatorInterface */
    protected static $sm;

    /**
     * Format column value
     *
     * @param array                   $data   Row data
     * @param array                   $column Column Parameters
     * @param ServiceLocatorInterface $sm     Service Manager
     *
     * @return string
     */
    public static function format($data, array $column = [], ServiceLocatorInterface $sm = null)
    {
        self::$sm = $sm;

        /** @var Url $urlHelper */
        $urlHelper = $sm->get('Helper\Url');

        switch($data['entityName']) {
            case self::ENTITY_LICENCE:
                $url = $urlHelper->fromRoute('licence', ['licence' => $data['entityPk']], [], true);
                break;
            case self::ENTITY_APPLICATION:
                $url = $urlHelper->fromRoute('lva-application', ['application' => $data['entityPk']], [], true);
                break;
            case self::ENTITY_TRANSPORT_MANAGER:
                $url = $urlHelper->fromRoute(
                    'transport-manager',
                    ['transportManager' => $data['entityPk']],
                    [],
                    true
                );
                break;
            case self::ENTITY_IRFO_GV_PERMIT:
                $url = $urlHelper->fromRoute(
                    'operator/irfo/gv-permits',
                    [
                        'organisation' => $data['organisationId'],
                        'action' => 'details',
                        'id' => $data['entityPk']
                    ],
                    [],
                    true
                );
                break;
            case self::ENTITY_IRFO_PSV_AUTH:
                $url = $urlHelper->fromRoute(
                    'operator/irfo/psv-authorisations',
                    [
                        'organisation' => $data['organisationId'],
                        'action' => 'edit',
                        'id' => $data['entityPk']
                    ],
                    [],
                    true
                );
                break;
            case self::ENTITY_ORGANISATION:
                $url = $urlHelper->fromRoute(
                    'operator/business-details',
                    ['organisation' => $data['organisationId']],
                    [],
                    true
                );
                break;
            case self::ENTITY_CASES:
                $url = $urlHelper->fromRoute(
                    'case',
                    ['action' => 'details', 'case' => $data['entityPk']],
                    [],
                    true
                );
                break;
            case self::ENTITY_BUS_REG:
                $url = $urlHelper->fromRoute(
                    'licence/bus-details',
                    [
                        'licence' => $data['licenceId'],
                        'busRegId' => $data['entityPk']
                    ],
                    [],
                    true
                );
                break;
            default:
                $url = null;
        }

        return self::getOutput(
            Escape::html($data['organisationId']),
            Escape::html($data['organisationName']),
            Escape::html($data['licenceId']),
            Escape::html($data['licNo']),
            Escape::html($data['entityName']),
            Escape::html($data['entityPk']),
            $url
        );
    }

    /**
     * render output for the table
     *
     * @param int         $organisationId   Organisation id
     * @param string      $organisationName Organisation name
     * @param int         $licenceId        Licence ID
     * @param string      $licNo            Licence number
     * @param string      $entityName       Entity name
     * @param string      $entityPk         Entity Primary Key
     * @param string|null $url              URL
     *
     * @return string
     */
    private static function getOutput(
        $organisationId,
        $organisationName,
        $licenceId,
        $licNo,
        $entityName,
        $entityPk,
        $url = null
    ) {
        $licenceNumber = self::getLicenceNumber($licenceId, $licNo, $url);
        $organisationName = self::getOrganisationName($organisationId, $organisationName, $url);

        if ($url === null) {
            return $organisationName .
                $licenceNumber .
                $entityName . ' ' .
                $entityPk;
        }

        return sprintf(
            $organisationName .
            $licenceNumber .
            sprintf('<a href="%s" target="_self">%s</a>', $url, ucfirst($entityName) . ' ' . $entityPk)
        );
    }

    /**
     * Get licence number value for output, if URL or non URL
     *
     * @param int    $licenceId     Licence ID
     * @param string $licenceNumber Licence number value
     * @param string $url           URL
     *
     * @return string
     */
    private static function getLicenceNumber($licenceId, $licenceNumber, $url = null)
    {
        if (empty($licenceId) || empty($licenceNumber)) {
            return '';
        }

        if (!is_null($url)) {
            /** @var Url $urlHelper */
            $urlHelper = self::$sm->get('Helper\Url');

            $url = $urlHelper->fromRoute(
                'licence',
                ['licence' => $licenceId],
                [],
                true
            );

            return sprintf(
                '<a href="%s" target="_self">%s</a>', $url, $licenceNumber
            ) . ' / ';
        }

        return $licenceNumber . ' / ';
    }

    /**
     * Get organisation name value for output, if URL or non URL
     *
     * @param int    $organisationId   Organisation ID
     * @param string $organisationName Organisation number value
     * @param string $url              URL
     *
     * @return string
     */
    private static function getOrganisationName($organisationId, $organisationName, $url = null)
    {
        if (empty($organisationId) || empty($organisationName)) {
            return '';
        }

        if (!is_null($url)) {
            /** @var Url $urlHelper */
            $urlHelper = self::$sm->get('Helper\Url');

            $url = $urlHelper->fromRoute(
                'operator/business-details',
                ['organisation' => $organisationId],
                [],
                true
            );

            return sprintf(
                '<a href="%s" target="_self">%s</a>',
                $url,
                $organisationName
            ) .
            ' / ';
        }

        return $organisationName . ' / ';
    }
}

<?php

namespace Common\Service\Table\Formatter;

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
                    ['transport-manager' => $data['entityPk']],
                    [],
                    true
                );
                break;
            case self::ENTITY_IRFO_GV_PERMIT:
                $url = $urlHelper->fromRoute(
                    'operator/irfo/gv-permits',
                    ['organisation' => $data['organisationId']],
                    [],
                    true
                );
                break;
            case self::ENTITY_IRFO_PSV_AUTH:
                $url = $urlHelper->fromRoute(
                    'operator/irfo/gv-permits',
                    ['organisation' => $data['organisationId']],
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
                $url = $urlHelper->fromRoute('case', ['action' => 'details', 'case' => $data['entityPk']], [], true);
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
            $data['organisationName'],
            $data['licNo'],
            $data['entityName'],
            $data['entityPk'],
            $url
        );
    }

    /**
     * render output for the table
     *
     * @param string      $organisationName Organisation name
     * @param string      $licNo            Licence number
     * @param string      $entityName       Entity name
     * @param string      $entityPk         Entity Primary Key
     * @param string|null $url              URL
     *
     * @return string
     */
    private static function getOutput(
        $organisationName,
        $licNo,
        $entityName,
        $entityPk,
        $url = null
    ) {
        $licenceNumber = self::getLicenceNumber($licNo, $url);

        if ($url === null) {
            return $organisationName . ' / ' .
                $licenceNumber . $entityName . ' / ' .
                $entityPk;
        }

        return sprintf('<a href="%s" target="_self">%s</a>', $url, $organisationName) . ' / ' .
            $licenceNumber .
            sprintf('<a href="%s" target="_self">%s</a>', $url, ucfirst($entityName)) . ' / ' .
            sprintf('<a href="%s" target="_self">%s</a>', $url, $entityPk);
    }

    /**
     * Get licence number value for output, if URL or non URL
     *
     * @param string $licenceNumber Licence number value
     * @param string $url           URL
     *
     * @return string
     */
    private static function getLicenceNumber($licenceNumber, $url = null)
    {
        if (empty($licenceNumber)) {
            return '';
        }

        if (!is_null($url)) {
            return sprintf(
                '<a href="%s" target="_self">%s</a>', $url, $licenceNumber
            ) . ' / ';
        }

        return $licenceNumber . ' / ';
    }
}

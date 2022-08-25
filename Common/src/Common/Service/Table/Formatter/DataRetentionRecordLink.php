<?php

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;
use Laminas\Mvc\Controller\Plugin\Url;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Common\View\Helper\Status as StatusHelper;

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

    const STATUS_DELETION = [
        'value' => 'Marked for deletion',
        'colour' => 'red'
    ];

    const STATUS_POSTPONED = [
        'value' => 'Postponed',
        'colour' => 'orange'
    ];

    const STATUS_REVIEW = [
        'value' => 'To review',
        'colour' => 'green'
    ];

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
        /**
         * @var Url          $urlHelper
         * @var StatusHelper $statusHelper
         */
        $urlHelper = $sm->get('Helper\Url');
        $statusHelper = $sm->get('ViewHelperManager')->get('status');

        switch ($data['entityName']) {
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

        $output = self::getOutput(
            Escape::html($data['organisationId']),
            Escape::html($data['organisationName']),
            Escape::html($data['licNo']),
            Escape::html($data['entityName']),
            Escape::html($data['entityPk']),
            $url
        );

        $statusInfo = self::getStatus($data['actionConfirmation'], $data['nextReviewDate']);
        $status = $statusHelper->__invoke($statusInfo);

        return $output . $status;
    }

    /**
     * render output for the table
     *
     * @param int         $organisationId   Organisation id
     * @param string      $organisationName Organisation name
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
        $licNo,
        $entityName,
        $entityPk,
        $url = null
    ) {
        $licenceNumber = self::getLicenceNumber($licNo, $url);
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
            sprintf('<a class="govuk-link" href="%s" target="_self">%s</a>', $url, ucfirst($entityName) . ' ' . $entityPk)
        );
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
            /** @var Url $urlHelper */
            $urlHelper = self::$sm->get('Helper\Url');

            $url = $urlHelper->fromRoute(
                'licence-no',
                ['licNo' => $licenceNumber],
                [],
                true
            );

            return sprintf(
                '<a class="govuk-link" href="%s" target="_self">%s</a>',
                $url,
                $licenceNumber
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
                '<a class="govuk-link" href="%s" target="_self">%s</a>',
                $url,
                $organisationName
            ) .
            ' / ';
        }

        return $organisationName . ' / ';
    }

    /**
     * Determine the status
     * This ought to come from backend ref data or be calculated by the entity, but not currently available.
     *
     * @param bool        $actionConfirmation action confirmation
     * @param string|null $nextReviewDate     next review date
     *
     * @return array
     */
    private static function getStatus($actionConfirmation, $nextReviewDate)
    {
        $status = self::STATUS_DELETION;

        if ($actionConfirmation === false) {
            $status = self::STATUS_POSTPONED;

            if (is_null($nextReviewDate) || new \DateTime($nextReviewDate) <= new \DateTime()) {
                $status = self::STATUS_REVIEW;
            }
        }

        return $status;
    }
}

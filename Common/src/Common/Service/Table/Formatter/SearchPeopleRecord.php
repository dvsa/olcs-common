<?php

namespace Common\Service\Table\Formatter;

use Common\Data\Object\Search\People;
use Common\RefData;
use Common\Service\Helper\UrlHelperService;
use Common\Util\Escape;
use ZfcRbac\Service\AuthorizationService;

/**
 * @package Common\Service\Table\Formatter
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class SearchPeopleRecord implements FormatterPluginManagerInterface
{
    private AuthorizationService $authService;
    private UrlHelperService $urlHelper;

    public function __construct(AuthorizationService $authService, UrlHelperService $urlHelper)
    {
        $this->authService = $authService;
        $this->urlHelper = $urlHelper;
    }

    /**
     *
     * @param array $data The row data.
     * @param array $column The column data.
     *
     * @return string
     */
    public function format($data, $column = [])
    {
        $showAsText = $this->authService->isGranted(RefData::PERMISSION_INTERNAL_IRHP_ADMIN);

        if (!empty($row['applicationId']) && !empty($row['licNo'])) {
            if ($showAsText) {
                return sprintf(
                    '%s / %s',
                    $this->formatCellLicNo($row, $this->urlHelper, $showAsText),
                    Escape::html($row['applicationId'])
                );
            }

            return sprintf(
                '%s / <a class="govuk-link" href="%s">%s</a>',
                $this->formatCellLicNo($row, $this->urlHelper),
                $this->urlHelper->fromRoute('lva-application', ['application' => $row['applicationId']]),
                Escape::html($row['applicationId'])
            );
        } elseif (!empty($row['tmId']) && $row['foundAs'] !== People::FOUND_AS_HISTORICAL_TM) {
            if ($showAsText) {
                $tmLink = sprintf('TM %s', Escape::html($row['tmId']));
            } else {
                $tmLink = sprintf(
                    '<a class="govuk-link" href="%s">TM %s</a>',
                    $this->urlHelper->fromRoute('transport-manager/details', ['transportManager' => $row['tmId']]),
                    Escape::html($row['tmId'])
                );
            }

            if (!empty($row['licNo'])) {
                $licenceLink = $this->formatCellLicNo($row, $this->urlHelper, $showAsText);
                return $tmLink . ' / ' . $licenceLink;
            }

            return $tmLink;
        } elseif (!empty($row['licTypeDesc']) && !empty($row['licStatusDesc'])) {
            if ($showAsText) {
                return sprintf(
                    '%s, %s<br />%s',
                    Escape::html($row['licNo']),
                    Escape::html($row['licTypeDesc']),
                    Escape::html($row['licStatusDesc'])
                );
            }

            return sprintf(
                '<a class="govuk-link" href="%s">%s</a>, %s<br />%s',
                $this->urlHelper->fromRoute('licence', ['licence' => $row['licId']]),
                Escape::html($row['licNo']),
                Escape::html($row['licTypeDesc']),
                Escape::html($row['licStatusDesc'])
            );
        } elseif (!empty($row['licNo'])) {
            return $this->formatCellLicNo($row, $this->urlHelper, $showAsText);
        } elseif (!empty($row['applicationId'])) {
            if ($showAsText) {
                return sprintf(
                    '%s, %s',
                    Escape::html($row['applicationId']),
                    Escape::html($row['appStatusDesc'])
                );
            }

            return sprintf(
                '<a class="govuk-link" href="%s">%s</a>, %s',
                $this->urlHelper->fromRoute('lva-application', ['application' => $row['applicationId']]),
                Escape::html($row['applicationId']),
                Escape::html($row['appStatusDesc'])
            );
        }
        return '';
    }

    /**
     * Formats a cell with a licence link based on licNo
     *
     * @param array     $row        data row
     * @param bool      $showAsText Whether to return text only
     *
     * @return string
     */
    public function formatCellLicNo($row, $showAsText = false)
    {
        if ($showAsText) {
            return Escape::html($row['licNo']);
        }

        return sprintf(
            '<a class="govuk-link" href="%s">%s</a>',
            $this->urlHelper->fromRoute('licence-no', ['licNo' => trim($row['licNo'])]),
            Escape::html($row['licNo'])
        );
    }
}

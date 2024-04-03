<?php

namespace Common\Service\Table\Formatter;

use Common\RefData;
use Common\Util\Escape;
use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;

/**
 * Licence status is shown slightly differently in selfserve, with certain statuses mapped to "expired" status
 */
class LicenceStatusSelfserve implements FormatterPluginManagerInterface
{
    private const MARKUP_FORMAT = '<span class="govuk-tag govuk-tag--%s">%s</span>';

    private TranslatorDelegator $translator;

    public function __construct(TranslatorDelegator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param array $row    Row data
     * @param array $column Column data
     *
     * @return     string
     * @inheritdoc
     */
    public function format($row, $column = null)
    {
        switch ($row['status']['id']) {
            case RefData::LICENCE_STATUS_VALID:
            case RefData::LICENCE_STATUS_SURRENDER_UNDER_CONSIDERATION:
                $statusClass = 'green';
                break;
            case RefData::LICENCE_STATUS_SUSPENDED:
            case RefData::LICENCE_STATUS_CURTAILED:
            case RefData::LICENCE_STATUS_UNDER_CONSIDERATION:
            case RefData::LICENCE_STATUS_GRANTED:
                $statusClass = 'orange';
                break;
            case RefData::LICENCE_STATUS_SURRENDERED:
            case RefData::LICENCE_STATUS_REVOKED:
            case RefData::LICENCE_STATUS_TERMINATED:
            case RefData::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT:
            case RefData::LICENCE_STATUS_WITHDRAWN:
            case RefData::LICENCE_STATUS_REFUSED:
            case RefData::LICENCE_STATUS_NOT_TAKEN_UP:
                $statusClass = 'red';
                break;
            case RefData::LICENCE_STATUS_CANCELLED:
            default:
                $statusClass = 'grey';
                break;
        }

        if ($row['status']['id'] !== RefData::LICENCE_STATUS_SURRENDER_UNDER_CONSIDERATION) {
            [$row, $statusClass] = $this->changeStateIfExpired($row, $statusClass);
        }

        return sprintf(
            self::MARKUP_FORMAT,
            $statusClass,
            Escape::html($this->translator->translate($row['status']['description']))
        );
    }

    protected function changeStateIfExpired(array $row, string $statusClass): array
    {
        if (isset($row['isExpired']) && $row['isExpired'] === true) {
            $row['status']['description'] = 'licence.status.expired';
            $statusClass = 'red';
        }

        if (isset($row['isExpiring']) && $row['isExpiring'] === true) {
            $row['status']['description'] = 'licence.status.expiring';
            $statusClass = 'red';
        }

        return [$row, $statusClass];
    }
}

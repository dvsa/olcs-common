<?php

declare(strict_types=1);

namespace Common\Service\Table\Formatter;

class ExternalConversationStatus implements FormatterPluginManagerInterface
{
    /**
     * status
     *
     * @param array $row Row data
     * @param array $column Column data
     *
     * @inheritdoc
     */
    public function format($row, $column = null): string
    {
        switch ($row['userContextStatus']) {
            case "NEW_MESSAGE":
                $tagColor = 'govuk-tag--red';
                break;
            case "OPEN":
                $tagColor = 'govuk-tag--blue';
                break;
            case "CLOSED":
                $tagColor = 'govuk-tag--grey';
                break;
            default:
                $tagColor = 'govuk-tag--green';
                break;
        }

        return sprintf(
            '<strong class="govuk-tag %s">%s</strong>',
            $tagColor,
            str_replace('_', ' ', $row['userContextStatus']),
        );
    }
}

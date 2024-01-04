<?php

/**
 * Internal conversation message
 */

namespace Common\Service\Table\Formatter;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Internal conversation message
 */
class InternalConversationMessage implements FormatterPluginManagerInterface
{
    /**
     * status
     *
     * @param array $row    Row data
     * @param array $column Column data
     *
     * @return     string
     * @inheritdoc
     */
    public function format($row, $column = null): string
    {
        if (!empty($row['createdBy']['team'])) {
            $sender_name = 'Case Worker';
        } elseif (!empty($row['createdBy']['contactDetails']['person'])) {
            $person = $row['createdBy']['contactDetails']['person'];
            $sender_name = $person['forename'] . " " . $person['familyName'];
        } else {
            $sender_name = $row['createdBy']['loginId'];
        }

        $latest_message_created_at = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $row["createdOn"]);
        $date = $latest_message_created_at->format('l j F Y \a\t H:ia');

        $row_template = '<div class="govuk-!-margin-bottom-6">
                <div class="govuk-summary-card">
                    <div class="govuk-summary-card__title-wrapper">
                        <h2 class="govuk-summary-card__title">%s</h2> <h2 class="govuk-summary-card__title govuk-summary-card__date">%s</h2>
                    </div>
                    <div class="govuk-summary-card__content">
                        <p class="govuk-body">%s</p>
                    </div>
                </div>
            </div>';

        return vsprintf(
            $row_template,
            [
                $sender_name,
                $date,
                nl2br($row['messagingContent']['text'])
            ]
        );
    }
}

<?php

/**
 * Internal conversation message
 */

namespace Common\Service\Table\Formatter;
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
    public function format($row, $column = null)
    {
        if($row['createdBy']['team']) {
            $sender_name = "Case Worker";
        } else if($person = $row["createdBy"]["contactDetails"]["person"]){
            $sender_name = $person["forename"] . " " . $person["familyName"];
        } else {
            $sender_name = $row["createdBy"]["loginId"];
        }

        $latestMessageCreatedOn = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $row["createdOn"]);
        $date = $latestMessageCreatedOn->format('l j F Y \a\t H:ia');

        $rowTemplate = '<div class="govuk-!-margin-bottom-6">
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
            $rowTemplate,
            [
                $sender_name,
                $date,
                nl2br($row["messagingContent"]["text"])
            ]
        );
    }
}

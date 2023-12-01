<?php

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;

/**
 * Internal licence permit reference formatter
 */
class ConversationMessage implements FormatterPluginManagerInterface
{
    private RefDataStatus $refDataStatus;

    public function __construct(RefDataStatus $refDataStatus)
    {
        $this->refDataStatus = $refDataStatus;
    }

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
       $html = '<div class="govuk-!-margin-bottom-6">
                    <div class="govuk-summary-card">
                        <div class="govuk-summary-card__title-wrapper">
                            <h2 class="govuk-summary-card__title">%s</h2> <h2 class="govuk-summary-card__title govuk-summary-card__date">%s</h2>
                        </div>
                        <div class="govuk-summary-card__content">
                            <p class="govuk-body">%s</p>
                        </div>
                    </div>
                </div>';

        $dateTimeUnix = !isset($row["createdOn"]) ? "2023-11-09T15:53:45+0000" : $row["createdOn"];

        $latestMessageCreatedOn = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $dateTimeUnix);
        $dtOutput = $latestMessageCreatedOn->format('l j F Y \a\t H:ia');            

        return vsprintf(
            $html,
            [   
                !isset($row['name']) ? "James Atkins" : $row['name'],
                $dtOutput,
                nl2br("Welcome To chat\r\nThis is my OLCS document", false)
            ]
        );
    }
}

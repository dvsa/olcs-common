<?php

/**
 * Internal conversation message
 */

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Util\Escape;

/**
 * Internal conversation message
 */
class InternalConversationMessage implements FormatterPluginManagerInterface
{
    private UrlHelperService $urlHelper;
    private RefDataStatus $refDataStatus;

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
        if($row["createdBy"]["team"]) { // Somehow use getUserType()?
            $sender_name = "Case Worker"; // Translation?
        } else if($person = $row["createdBy"]["contactDetails"]["person"]){
            $sender_name = $person["forename"] . " " . $person["familyName"]; // Somehow use Person->getFullName()?
        } else {
            $sender_name = $row["createdBy"]["loginId"];
        }

        $latestMessageCreatedOn = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $row["createdOn"]);
        $date = $latestMessageCreatedOn->format('l j F Y \a\t H:ia');

        $rowTemplate = '%s %s %s';

        return vsprintf(
            $rowTemplate,
            [
                $sender_name,
                $date,
                $row["messagingContent"]["text"]
            ]
        );
    }
}

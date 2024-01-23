<?php

declare(strict_types=1);

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Util\Escape;
use DateTimeImmutable;
use DateTimeInterface;

class ExternalConversationLink implements FormatterPluginManagerInterface
{
    private UrlHelperService $urlHelper;

    public function __construct(UrlHelperService $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    /**
     * status
     *
     * @param array $row Row data
     * @param array $column Column data
     *
     * @return     string
     * @inheritdoc
     */
    public function format($row, $column = null): string
    {
        $route = 'conversations/view';
        $params = [
            'conversationId' => $row['id'],
        ];

        $statusCSS = '';

        switch ($row['userContextStatus']) {
            case "NEW_MESSAGE":
                $statusCSS = 'govuk-!-font-weight-bold';
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

        $rows = '<a class="' . 'govuk-body govuk-link govuk-!-padding-right-1 ' . $statusCSS . '" href="%s">%s: %s</a>
                <strong class="govuk-tag ' . $tagColor . '">
                    %s
                </strong>
                <br>';
        $rows = $rows . '<p class="govuk-body govuk-!-margin-1">%s</p>';

        $latestMessageCreatedOn = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $row["createdOn"]);
        $dtOutput = $latestMessageCreatedOn->format('l j F Y \a\t H:ia');

        if (isset($row['task']['application']['id'])) {
            $idMatrix = Escape::html($row['task']['licence']['licNo'] . " / " . $row['task']['application']['id']);
        } else {
            $idMatrix = Escape::html($row['task']['licence']['licNo']);
        }

        return vsprintf(
            $rows,
            [
                $this->urlHelper->fromRoute($route, $params),
                $idMatrix,
                $row["subject"],
                str_replace('_', ' ', $row['userContextStatus']),
                $dtOutput,
            ],
        );
    }
}

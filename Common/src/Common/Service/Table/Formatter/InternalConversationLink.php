<?php

declare(strict_types=1);

/**
 * Internal licence conversation link
 */

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Util\Escape;
use DateTimeImmutable;
use DateTimeInterface;
use Laminas\Router\Http\RouteMatch;

/**
 * Internal licence conversation link
 */
class InternalConversationLink implements FormatterPluginManagerInterface
{
    private UrlHelperService $urlHelper;
    private RefDataStatus $refDataStatus;
    private RouteMatch $route;

    public function __construct(UrlHelperService $urlHelper, RefDataStatus $refDataStatus, RouteMatch $route)
    {
        $this->urlHelper = $urlHelper;
        $this->refDataStatus = $refDataStatus;
        $this->route = $route;
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
    public function format($row, $column = null)
    {
        switch ($this->route->getParam('type')) {
            case 'application':
                $route = 'lva-application/conversation/view';
                $params = [
                    'application' => $row['task']['application']['id'],
                ];
                break;
            case 'licence':
                $route = 'licence/conversation/view';
                $params = [
                    'licence' => $row['task']['licence']['id'],
                ];
                break;
            case 'case':
                $route = 'case_conversation/view';
                $params = [
                    'licence' => $row['task']['licence']['id'],
                    'case'    => $this->route->getParam('case'),
                ];
                break;
            case 'irhp-application':
                $route = 'licence/irhp-application-conversation/view';
                $params = [
                    'licence' => $row['task']['licence']['id'],
                ];
                break;
        }

        $params['conversation'] = $row['id'];

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

        $latestMessageCreatedOn = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $row["createdOn"]);
        $dtOutput = $latestMessageCreatedOn->format('l j F Y \a\t H:ia');

        if (isset($row['task']['application']['id'])) {
            $idMatrix = Escape::html($row['task']['licence']['licNo'] . " / " . $row['task']['application']['id']);
        } else {
            $idMatrix = Escape::html($row['task']['licence']['licNo']);
        }

        $html = '
            <a class="govuk-body govuk-link govuk-!-padding-right-1 %s" href="%s">%s: %s</a>
            <strong class="govuk-tag %s">%s</strong>
            <br>
            <p class="govuk-body govuk-!-margin-1">%s</p>
        ';

        return sprintf(
            $html,
            $statusCSS,
            $this->urlHelper->fromRoute($route, $params),
            $idMatrix,
            $row["subject"],
            $tagColor,
            str_replace('_', ' ', $row['userContextStatus']),
            $dtOutput,
        );
    }
}

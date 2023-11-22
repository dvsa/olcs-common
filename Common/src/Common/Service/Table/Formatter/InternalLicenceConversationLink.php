<?php

/**
 * Internal licence permit reference formatter
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Util\Escape;

/**
 * Internal licence permit reference formatter
 */
class InternalLicenceConversationLink implements FormatterPluginManagerInterface
{
    private UrlHelperService $urlHelper;
    private RefDataStatus $refDataStatus;

    /**
     * @param UrlHelperService $urlHelper
     */
    public function __construct(UrlHelperService $urlHelper, RefDataStatus $refDataStatus)
    {
        $this->urlHelper = $urlHelper;
        $this->refDataStatus = $refDataStatus;
       // $this->dateTime = $dateTime;
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

        $route = 'licence/conversation/view';
        $licence = $row['task']['licence']['id'];
        $params = [
            'licence' => $licence,
            'conversation' => $row['id']
        ];
        $statusCSS = $row['userContextStatus'] == "NEW_MESSAGE" ? 'govuk-!-font-weight-bold' : '';
        $rows ='<a class="'.'govuk-body govuk-link govuk-!-padding-right-1 '. $statusCSS.'" href="%s">%s: %s</a>
                <strong class="govuk-tag govuk-tag--red">
                    %s
                </strong>
                <br>';
        $rows = $rows. '<p class="govuk-body govuk-!-margin-1">%s</p>';
       
        $latestMessageCreatedOn = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $row["createdOn"]);
        $dtOutput = $latestMessageCreatedOn->format('l j F Y \a\t H:ia');
        
        if(isset($row['task']['application']['id']))
        {
           $idMatrix = Escape::html($row['task']['licence']['licNo'] . " / " . $row['task']['application']['id']);
        }
        else{
            $idMatrix = Escape::html($row['task']['licence']['licNo']);
        }
        
        return vsprintf(
            $rows,
            [
                $this->urlHelper->fromRoute($route, $params), // route
                $idMatrix, //id 
                $row["subject"],
                str_replace('_',' ',$row['userContextStatus']), //status
                $dtOutput // date time stamp
            ]
        );
    }
}

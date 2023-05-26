<?php

/**
 * Note URL formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Laminas\Http\Request;

/**
 * Note URL formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class NoteUrl implements FormatterPluginManagerInterface
{
    protected Request $request;
    protected UrlHelperService $urlHelper;

    /**
     * @param Request          $request
     * @param UrlHelperService $urlHelper
     */
    public function __construct(Request $request, UrlHelperService $urlHelper)
    {
        $this->request = $request;
        $this->urlHelper = $urlHelper;
    }

    /**
     * Format a note URL
     *
     * @param      array $row
     * @param      array $column
     * @return     string
     * @inheritdoc
     */
    public function format($row, $column = [])
    {
        $url = $this->urlHelper->fromRoute(
            null,
            ['action' => 'edit', 'id' => $row['id']],
            ['query' => $this->request->getQuery()->toArray()],
            true
        );

        return '<a class="govuk-link js-modal-ajax" href="' . $url . '">'
        . (new \DateTime($row['createdOn']))->format(\DATE_FORMAT) . '</a>';
    }
}

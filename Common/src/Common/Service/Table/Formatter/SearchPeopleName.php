<?php

namespace Common\Service\Table\Formatter;

use Common\Data\Object\Search\People;
use Common\Service\Helper\UrlHelperService;

/**
 * @package Common\Service\Table\Formatter
 *
 *
 */
class SearchPeopleName implements FormatterPluginManagerInterface
{
    public function __construct(protected UrlHelperService $urlHelper)
    {
    }

    /**
     *
     * @param array $data   The row data.
     * @param array $column The column data.
     *
     * @return string
     */
    public function format($data, $column = [])
    {
        if ($data['foundAs'] === People::FOUND_AS_HISTORICAL_TM) {
            return sprintf(
                '<a class="govuk-link" href="%s">%s</a>',
                $this->urlHelper->fromRoute('historic-tm', ['historicId' => $data['tmId']]),
                $data['personFullname']
            );
        }
        return $data['personFullname'];
    }
}

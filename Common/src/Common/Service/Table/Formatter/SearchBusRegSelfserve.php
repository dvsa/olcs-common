<?php

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Util\Escape;
use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;

/**
 * Class AccessedCorrespondence
 *
 * Accessed correspondence formatter, displays correspondence as a link to the document and
 * denotes whether the correspondence has been accessed.
 *
 * @package Common\Service\Table\Formatter
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class SearchBusRegSelfserve implements FormatterPluginManagerInterface
{
    private UrlHelperService $urlHelper;
    private TranslatorDelegator $translator;

    /**
     * @param UrlHelperService $urlHelper
     */
    public function __construct(UrlHelperService $urlHelper, TranslatorDelegator $translator)
    {
        $this->urlHelper = $urlHelper;
        $this->translator = $translator;
    }

    /**
     * Get a link for the document with the access indicator.
     *
     * @param array $data The row data.
     * @param array $column The column data.
     *
     * @return string The document link and accessed indicator
     */
    public function format($data, $column = [])
    {
        $url = $this->urlHelper->fromRoute(
            'search-bus/details',
            ['busRegId' => $data['busregId']]
        );

        return sprintf(
            '<a class="govuk-link" href="%s">%s</a><br/>%s',
            $url,
            Escape::html($data['regNo']),
            $this->translator->translate($data['busRegStatus'])
        );
    }
}
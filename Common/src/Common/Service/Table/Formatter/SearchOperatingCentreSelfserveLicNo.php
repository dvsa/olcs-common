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
class SearchOperatingCentreSelfserveLicNo implements FormatterPluginManagerInterface
{
    private TranslatorDelegator $translator;

    /**
     * @param TranslatorDelegator $translator
     */
    public function __construct(TranslatorDelegator $translator)
    {
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
        return sprintf(
            '<a class="govuk-link" href="%s">%s</a><br/>%s',
            '/view-details/licence/' . $data['licId'],
            Escape::html($data['licNo']),
            $this->translator->translate($data['licStatusDesc'])
        );
    }
}

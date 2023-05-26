<?php

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;

/**
 * Document Description Formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DocumentDescription implements FormatterPluginManagerInterface
{
    private TranslatorDelegator $translator;
    private UrlHelperService $urlHelper;

    /**
     * @param TranslatorDelegator $translator
     * @param UrlHelperService    $urlHelper
     */
    public function __construct(TranslatorDelegator $translator, UrlHelperService $urlHelper)
    {
        $this->translator = $translator;
        $this->urlHelper = $urlHelper;
    }
    /**
     * Format a cell
     *
     * @param array $data   Row data
     * @param array $column Column data
     *
     * @return string
     */
    public function format($data, $column = [])
    {
        if (!isset($data['documentStoreIdentifier']) || empty($data['documentStoreIdentifier'])) {
            return $this->getAnchor($data, $this->translator);
        }

        $url = $this->urlHelper->fromRoute(
            'getfile',
            [
                'identifier' => $data['id']
            ]
        );

        $attr = '';

        if (preg_match('/\.html$/', $data['documentStoreIdentifier'])) {
            $attr = 'target="_blank"';
        }

        return '<a class="govuk-link" href="' . $url . '" ' . $attr . '>' . $this->getAnchor($data, $this->translator) . '</a>';
    }

    /**
     * Get anchor
     *
     * @param array $data Data
     *
     * @return string
     */
    private function getAnchor($data, $translator)
    {
        if (isset($data['description'])) {
            return $data['description'];
        }
        if (isset($data['filename'])) {
            return basename($data['filename']);
        }

        return $translator->translate('internal.document-description.formatter.no-description');
    }
}

<?php

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\DataHelperService;
use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;

/**
 * Translate formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Translate implements FormatterPluginManagerInterface
{
    private TranslatorDelegator $translator;

    private DataHelperService $dataHelper;

    public function __construct(TranslatorDelegator $translator, DataHelperService $dataHelper)
    {
        $this->translator = $translator;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Translate value
     *
     * @param array $data   Data
     * @param array $column Column parameters
     *
     * @return string
     */
    public function format($data, $column = [])
    {
        if (isset($column['name'])) {
            return $this->translator->translate(
                $this->dataHelper->fetchNestedData($data, $column['name'])
            );
        }

        if (isset($column['content'])) {
            return $this->translator->translate($column['content']);
        }

        return '';
    }
}

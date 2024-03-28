<?php

namespace Common\Service\Table\Formatter;

use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;

/**
 * RefData formatter
 */
class RefData implements FormatterPluginManagerInterface
{
    private TranslatorDelegator $translator;

    public function __construct(TranslatorDelegator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Format a address
     *
     * @param array $data   Row data
     * @param array $column Column params
     *
     * @return string
     */
    public function format($data, $column = [])
    {

        $colData = $data[$column['name']];
        if (empty($colData)) {
            return '';
        }

        //  single RefData (check, it is NOT an array of entities)
        if (isset($colData['description'])) {
            return $this->translator->translate($colData['description']);
        }

        //  array of RefData
        $result = [];
        foreach ($colData as $row) {
            $result[] = $this->translator->translate($row['description']);
        }

        $sprtr = ($column['separator'] ?? '');

        return implode($sprtr, $result);
    }
}

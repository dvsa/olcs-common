<?php

namespace Common\Service\Table\Formatter;

use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;

/**
 * ConditionsUndertakingsType
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ConditionsUndertakingsType implements FormatterPluginManagerInterface
{
    private TranslatorDelegator $translator;

    public function __construct(TranslatorDelegator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Get the condition undertaking type and add Schedule 4/1 text if applicable
     *
     * @param array $data   The row data.
     * @param array $column The column data.
     *
     * @return mixed
     */
    public function format($data, $column = [])
    {
        // supress PMD warning
        unset($column);

        $content = $data['conditionType']['description'];

        if ($data['s4'] !== null) {
            $content .= '<br>' . $this->translator->translate('(Schedule 4/1)');
        }

        return $content;
    }
}

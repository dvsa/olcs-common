<?php

namespace Common\Service\Table\Formatter;

/**
 * ConditionsUndertakingsType
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ConditionsUndertakingsType implements FormatterInterface
{
    /**
     * Get the condition undertaking type and add Schedule 4/1 text if applicable
     *
     * @param array $data The row data.
     * @param array $column The column data.
     * @param null $sm The service manager.
     *
     * @return mixed
     */
    public static function format($data, $column = array(), $sm = null)
    {
        // supress PMD warning
        unset($column);

        $content = $data['conditionType']['description'];

        if ($data['s4'] !== null) {
            $content .= '<br>'. $sm->get('translator')->translate('(Schedule 4/1)');
        }

        return $content;
    }
}

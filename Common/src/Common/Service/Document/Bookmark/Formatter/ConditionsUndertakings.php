<?php

namespace Common\Service\Document\Bookmark\Formatter;

use Common\Service\Entity\ConditionUndertakingEntityService;

/**
 * Conditions & Undertakings formatter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ConditionsUndertakings implements FormatterInterface
{
    public static function format(array $data)
    {
        $rows = [
            ConditionUndertakingEntityService::TYPE_CONDITION => ['Conditions'],
            ConditionUndertakingEntityService::TYPE_UNDERTAKING => ['Undertakings']
        ];

        foreach ($data as $row) {
            $key = $row['conditionType']['id'];
            $index = count($rows[$key]);

            $rows[$key][] = sprintf("%d).\t%s", $index, $row['notes']);
        }

        $results = [];
        foreach ($rows as $key => $data) {
            if (count($data) > 1) {
                $results[] = implode("\n\n", $data);
            }
        }

        return implode("\n\n", $results);
    }
}

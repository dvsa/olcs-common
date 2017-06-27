<?php

namespace Common\Service\Table\Formatter;

/**
 * @author Dmitry Golubev <d.e.golubev@gmail.com>
 */
class CaseEntityName implements FormatterInterface
{
    /**
     * Return traffic area name
     *
     * @param array                               $data   Data
     * @param array                               $column Column data
     * @param \Zend\ServiceManager\ServiceManager $sm     Service manager
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if ($data['caseType']['id'] === \Common\RefData::CASE_TYPE_TM) {
            if (empty($data['transportManager']['homeCd']['person'])) {
                return '';
            }

            $person = $data['transportManager']['homeCd']['person'];
            $title = (isset($person['title']) ? $person['title'] : null);

            return implode(
                ' ',
                array_filter(
                    [
                        $title ? $title['description'] : null,
                        $person['forename'],
                        $person['familyName'],
                    ]
                )
            );
        }

        return $data['licence']['organisation']['name'];
    }
}

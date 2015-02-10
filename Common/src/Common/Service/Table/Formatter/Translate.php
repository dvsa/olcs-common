<?php

/**
 * Translate formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Table\Formatter;

/**
 * Translate formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Translate implements FormatterInterface
{
    /**
     * Translate value
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if (isset($column['name'])) {

            $name = $column['name'];
            while (strstr($name, '->')) {
                list($index, $name) = explode('->', $name, 2);

                $data = $data[$index];
            }

            return $sm->get('translator')->translate($data[$name]);
        }

        if (isset($column['content'])) {
            return $sm->get('translator')->translate($column['content']);
        }

        return '';
    }
}

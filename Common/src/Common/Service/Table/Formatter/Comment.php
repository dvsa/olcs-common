<?php

/**
 * Comment formatter
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * Comment formatter
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class Comment implements FormatterInterface
{
    /**
     * Comment value
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $content = '';

        if (isset($data[$column['name']]) && !is_null($data[$column['name']])) {

            if (isset($column['maxlength'])
                && is_numeric($column['maxlength'])
                && strlen($data[$column['name']]) > $column['maxlength']
            ) {
                $content = substr($data[$column['name']], 0, $column['maxlength']);

                if (isset($column['append'])) {
                    $content .= $column['append'];
                } else {
                    $content .= '...';
                }

                return nl2br($content);
            }

            return nl2br($data[$column['name']]);
        }
        return '';
    }
}

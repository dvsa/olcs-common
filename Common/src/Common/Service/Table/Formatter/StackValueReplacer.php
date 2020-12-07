<?php

/**
 * Stack Value Replacer formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Table\Formatter;

/**
 * Stack Value Replacer formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class StackValueReplacer implements FormatterInterface
{
    /**
     * Retrieve a nested value
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $stringFormat = $column['stringFormat'];

        if (preg_match_all('/(\{([a-zA-Z0-9\-\>]+)\})+/', $stringFormat, $matches)) {
            foreach (array_keys($matches[0]) as $key) {
                $stringFormat = str_replace(
                    $matches[1][$key],
                    StackValue::format($data, ['stack' => $matches[2][$key]], $sm),
                    $stringFormat
                );
            }
        }

        return $stringFormat;
    }
}

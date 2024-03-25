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
class StackValueReplacer implements FormatterPluginManagerInterface
{
    private StackValue $stackValueFormatter;

    /**
     * @param Sum       $sumFormatter
     * @param FeeAmount $feeAmountFormatter
     */
    public function __construct(StackValue $stackValueFormatter)
    {
        $this->stackValueFormatter = $stackValueFormatter;
    }

    /**
     * Retrieve a nested value
     *
     * @param  array $data
     * @param  array $column
     * @return string
     */
    public function format($data, $column = [])
    {
        $stringFormat = $column['stringFormat'];

        if (preg_match_all('/(\{([a-zA-Z0-9\-\>]+)\})+/', $stringFormat, $matches)) {
            foreach (array_keys($matches[0]) as $key) {
                $stringFormat = str_replace(
                    $matches[1][$key],
                    $this->stackValueFormatter->format($data, ['stack' => $matches[2][$key]]),
                    $stringFormat
                );
            }
        }

        return $stringFormat;
    }
}

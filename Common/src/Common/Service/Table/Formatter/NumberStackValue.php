<?php
declare(strict_types=1);

namespace Common\Service\Table\Formatter;

class NumberStackValue extends StackValue implements FormatterInterface
{
    public static function format($data, $column = array(), $sm = null)
    {
        return number_format(parent::format($data, $column, $sm));
    }
}

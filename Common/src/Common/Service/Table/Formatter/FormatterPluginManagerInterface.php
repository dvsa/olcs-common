<?php

namespace Common\Service\Table\Formatter;

interface FormatterPluginManagerInterface
{
    public function format($data, $column = []);
}

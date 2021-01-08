<?php

namespace Common\Service\Qa;

use Common\Service\Helper\TranslationHelperService;
use RuntimeException;
use Laminas\View\Helper\AbstractHelper;

class TranslateableTextParameterHandler
{
    /** @var array */
    private $formatters;

    /**
     * Create service instance
     *
     * @return TranslateableTextParameterHandler
     */
    public function __construct()
    {
        $this->formatters = [];
    }

    /**
     * Resolve a final parameter value using a value and optional formatter function
     *
     * @param array $translateableTextParameter
     *
     * @return string
     */
    public function handle(array $translateableTextParameter)
    {
        $value = $translateableTextParameter['value'];

        if (!isset($translateableTextParameter['formatter'])) {
            return $value;
        }

        $formatterName = $translateableTextParameter['formatter'];

        if (!isset($this->formatters[$formatterName])) {
            throw new RuntimeException('Unknown formatter ' . $formatterName);
        }

        $formatterInvokable = $this->formatters[$formatterName];

        return $formatterInvokable->__invoke($value);
    }

    /**
     * Register an formatter corresponding to the supplied name
     *
     * @param string $name
     * @param AbstractHelper $formatter
     */
    public function registerFormatter($name, AbstractHelper $formatter)
    {
        $this->formatters[$name] = $formatter;
    }
}

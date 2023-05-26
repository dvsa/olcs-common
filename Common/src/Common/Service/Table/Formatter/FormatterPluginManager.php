<?php

namespace Common\Service\Table\Formatter;

use Laminas\ServiceManager\AbstractPluginManager;

class FormatterPluginManager extends AbstractPluginManager
{
    protected $instanceOf = FormatterPluginManagerInterface::class;

    protected $aliases = [
    ];

    protected $factories = [
    ];

    public function validatePlugin($plugin)
    {
        // TODO: Implement validatePlugin() method for formatters if we need one?
    }
}

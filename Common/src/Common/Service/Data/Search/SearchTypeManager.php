<?php

namespace Common\Service\Data\Search;

use Common\Data\Object\Search\SearchAbstract;
use Dvsa\Olcs\Utils\Traits\PluginManagerTrait;
use Laminas\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager for search data objects
 *
 * Class SearchTypeManager
 * @package Olcs\Service\Data\Search
 */
class SearchTypeManager extends AbstractPluginManager
{
    use PluginManagerTrait;

    protected $instanceOf = SearchAbstract::class;

    /**
     * Do NOT allow any class which hasn't been explicitly registered to be used as a search type. Changing this to
     * true will probably introduce a security flaw.
     *
     * @var bool
     */
    protected $autoAddInvokableClass = false;
}

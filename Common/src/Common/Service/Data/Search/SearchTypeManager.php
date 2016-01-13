<?php

namespace Common\Service\Data\Search;

use Common\Data\Object\Search\SearchAbstract;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

/**
 * Plugin manager for search data objects
 *
 * Class SearchTypeManager
 * @package Olcs\Service\Data\Search
 */
class SearchTypeManager extends AbstractPluginManager
{
    /**
     * Do NOT allow any class which hasn't been explicitly registered to be used as a search type. Changing this to
     * true will probably introduce a security flaw.
     *
     * @var bool
     */
    protected $autoAddInvokableClass = false;

    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof SearchAbstract) {
            return;
        }

        throw new Exception\RuntimeException('Invalid class');
    }
}

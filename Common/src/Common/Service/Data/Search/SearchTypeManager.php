<?php

namespace Common\Service\Data\Search;

use Common\Data\Object\Search\SearchAbstract;
use Laminas\ServiceManager\AbstractPluginManager;
use Psr\Container\ContainerInterface;

/**
 * Plugin manager for search data objects
 *
 * Class SearchTypeManager
 * @package Olcs\Service\Data\Search
 */
class SearchTypeManager extends AbstractPluginManager
{
    protected $instanceOf = SearchAbstract::class;

    /**
     * Do NOT allow any class which hasn't been explicitly registered to be used as a search type. Changing this to
     * true will probably introduce a security flaw.
     *
     * @var bool
     */
    protected $autoAddInvokableClass = false;

    private array $config;

    public function __construct(ContainerInterface $container, $config)
    {
        $this->config = $config;
        parent::__construct($container, $config);
    }

    /**
     * @todo this isn't great, but provides for backward compatibility with old code as we move to Laminas 3
     *
     * the old code (mis)used a debug method within the Laminas service manager to provide this info,
     * for now we're replicating that behaviour here
     */
    public function getRegisteredServices(): array
    {
        $factories = $this->config['factories'] ?? [];
        $invokables = $this->config['invokables'] ?? [];

        return array_keys(
            array_merge($factories, $invokables)
        );
    }
}

<?php

namespace Common\Service\Table\Formatter;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class TaskCheckboxFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return TaskCheckbox
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $tableBuilder = $container->get('TableBuilder');
        return new TaskCheckbox($tableBuilder);
    }
}

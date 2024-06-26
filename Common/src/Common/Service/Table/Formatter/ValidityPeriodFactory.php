<?php

namespace Common\Service\Table\Formatter;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ValidityPeriodFactory implements FactoryInterface
{
    /**
     * @param  $requestedName
     * @param  array|null         $options
     * @return ValidityPeriod
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $viewHelperManager = $container->get('ViewHelperManager');
        $translator = $container->get('translator');
        return new ValidityPeriod($viewHelperManager, $translator);
    }
}

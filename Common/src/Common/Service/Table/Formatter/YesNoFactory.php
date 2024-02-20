<?php

namespace Common\Service\Table\Formatter;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class YesNoFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return YesNo
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $stackHelper = $container->get('Helper\Stack');
        $translator = $container->get('translator');
        return new YesNo($stackHelper, $translator);
    }
}

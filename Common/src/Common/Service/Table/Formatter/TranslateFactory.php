<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;

class TranslateFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $translator = $container->get('translator');
        $dataHelper = $container->get('Helper\Data');
        return new Translate($translator, $dataHelper);
    }
}

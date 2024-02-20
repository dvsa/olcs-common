<?php

namespace Common\View\Helper;

use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class TranslateReplaceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return TranslateReplace
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TranslateReplace
    {
        $translator = $container->get('Helper\Translation');

        return new TranslateReplace($translator);
    }
}

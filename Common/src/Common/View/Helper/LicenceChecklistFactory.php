<?php

namespace Common\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class LicenceChecklistFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return LicenceChecklist
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LicenceChecklist
    {
        $viewHelperManager = $container->get('ViewHelperManager');
        $translator = $viewHelperManager->get('translate');

        return new LicenceChecklist($translator);
    }
}

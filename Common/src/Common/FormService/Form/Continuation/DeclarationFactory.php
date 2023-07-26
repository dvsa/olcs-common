<?php

declare(strict_types=1);

namespace Common\FormService\Form\Continuation;

use Common\Service\Helper\FormHelperService;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Helper\UrlHelperService;
use Common\Service\Script\ScriptFactory;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class DeclarationFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return Declaration
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Declaration
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }
        $translationHelper = $container->get(TranslationHelperService::class);
        $formHelper = $container->get(FormHelperService::class);
        $scriptFactory = $container->get(ScriptFactory::class);
        $urlHelper = $container->get(UrlHelperService::class);
        return new Declaration($formHelper, $translationHelper, $scriptFactory, $urlHelper);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return Declaration
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): Declaration
    {
        return $this->__invoke($serviceLocator, Declaration::class);
    }
}

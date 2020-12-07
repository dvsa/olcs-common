<?php

namespace Common\View\Factory\Helper;

use Common\View\Helper\EscapeHtml;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class EscapeHtmlFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EscapeHtml
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, EscapeHtml::class);
    }

    /**
     * Invoke
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return EscapeHtml
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new EscapeHtml($container->getServiceLocator()->get('HtmlPurifier'));
    }
}

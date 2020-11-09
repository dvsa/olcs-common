<?php

namespace Common\Service\Utility;

use HTMLPurifier;
use HTMLPurifier_Config;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Html Purifier Factory
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class HtmlPurifierFactory implements FactoryInterface
{
    protected $whiteList =
        'a[href|class|id|target],p[class|style|id],b,i[class|style|id],strong,br,span[class|style|id],h1[class|id],h2[class|id],h3[class|id],h4[class|id],li[class|style|id],ul[class|style|id]';

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return HTMLPurifier
     */
    public function createService(ServiceLocatorInterface $serviceLocator): HTMLPurifier
    {
        $config = $serviceLocator->get('Config');
        return $this($serviceLocator, HTMLPurifier::class, ['html-purifier-cache-dir' => $config['html-purifier-cache-dir']]);
    }

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options

     * @return HTMLPurifier
     */
    public function __invoke(ContainerInterface $container, string $requestedName, array $options = null): HTMLPurifier
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', $options['html-purifier-cache-dir']);
        $config->set('HTML.Allowed', $this->whiteList);
        return new HTMLPurifier($config);
    }
}

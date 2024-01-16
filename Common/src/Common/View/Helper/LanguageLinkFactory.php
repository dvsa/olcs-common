<?php

namespace Common\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class LanguageLinkFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return LanguageLink
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LanguageLink
    {
        $languagePref = $container->get('LanguagePreference');

        return new LanguageLink($languagePref);
    }
}

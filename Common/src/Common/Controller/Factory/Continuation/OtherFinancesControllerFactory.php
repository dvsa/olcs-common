<?php

namespace Common\Controller\Factory\Continuation;

use Common\Controller\Continuation\OtherFinancesController;
use Common\FormService\FormServiceManager;
use Common\Service\Helper\FormHelperService;
use Common\Service\Helper\GuidanceHelperService;
use Common\Service\Helper\TranslationHelperService;
use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcRbacMvc\Service\AuthorizationService;

class OtherFinancesControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return OtherFinancesController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): OtherFinancesController
    {
        $niTextTranslationUtil = $container->get(NiTextTranslation::class);
        $authService = $container->get(AuthorizationService::class);
        $formServiceManager = $container->get(FormServiceManager::class);
        $translationHelper = $container->get(TranslationHelperService::class);
        $formHelper = $container->get(FormHelperService::class);
        $guidanceHelper = $container->get(GuidanceHelperService::class);
        return new OtherFinancesController($niTextTranslationUtil, $authService, $formServiceManager, $translationHelper, $formHelper, $guidanceHelper);
    }
}

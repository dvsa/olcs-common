<?php

namespace Common\Controller\Factory\Continuation;

use Common\Controller\Continuation\ReviewController;
use Common\FormService\FormServiceManager;
use Common\Service\Helper\TranslationHelperService;
use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcRbacMvc\Service\AuthorizationService;

class ReviewControllerFactory implements FactoryInterface
{
    /**
     * @param $requestedName
     * @param array|null $options
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ReviewController
    {
        $niTextTranslationUtil = $container->get(NiTextTranslation::class);
        $authService = $container->get(AuthorizationService::class);
        $formServiceManager = $container->get(FormServiceManager::class);
        $translationHelper = $container->get(TranslationHelperService::class);
        return new ReviewController($niTextTranslationUtil, $authService, $formServiceManager, $translationHelper);
    }
}

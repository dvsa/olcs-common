<?php

namespace Common\Controller\Lva\Factories\Controller;

use Common\Controller\Lva\ReviewController;
use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcRbacMvc\Service\AuthorizationService;

class ReviewControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ReviewController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ReviewController
    {
        $niTextTranslationUtil = $container->get(NiTextTranslation::class);
        $authService = $container->get(AuthorizationService::class);
        return new ReviewController(
            $niTextTranslationUtil,
            $authService
        );
    }
}

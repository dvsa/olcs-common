<?php

namespace Common\Controller\Plugin;

use Common\Service\Cqrs\Query\QuerySender;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class FeaturesEnabledForMethod
 * @package Common\Controller\Plugin
 */
class FeaturesEnabledForMethod extends AbstractPlugin
{
    /**
     * @var QuerySender
     */
    private $querySender;

    /**
     * @param QuerySender $sender
     */
    public function __construct(QuerySender $sender)
    {
        $this->querySender = $sender;
    }

    /**
     * @param array  $toggleConfig
     * @param string $method
     *
     * @return bool
     */
    public function __invoke(array $toggleConfig, $method): bool
    {
        //check for config specific to the action
        if (isset($toggleConfig[$method])) {
            if (!empty($toggleConfig[$method])) {
                return $this->querySender->featuresEnabled($toggleConfig[$method]);
            }

            return true;
        }
        return false;
    }
}

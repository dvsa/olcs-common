<?php

/**
 * ApplicationRevive.php
 */
namespace Common\BusinessService\Service\Lva;

use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\Response;

use Common\Service\Entity\ApplicationEntityService;
use Common\Service\Entity\LicenceEntityService;

/**
 * Class ApplicationRevive
 *
 * Revive an application.
 *
 * @package Common\BusinessService\Service\Lva
 */
class ApplicationRevive implements BusinessServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Process the application revive request and set the appropricate application
     * and licence statuses.
     *
     * @param array $params
     *
     * @return Response
     */
    public function process(array $params = array())
    {
        $response = new Response();

        if (!isset($params['application'])) {
            $response->setType(Response::TYPE_FAILED);
            return $response;
        }

        $applicationEntityService = $this->getServiceLocator()->get('Entity\Application');

        switch ($params['application']['status']['id']) {
            case ApplicationEntityService::APPLICATION_STATUS_NOT_TAKEN_UP:
                $licenceStatus = LicenceEntityService::LICENCE_STATUS_GRANTED;
                $applicationStatus = ApplicationEntityService::APPLICATION_STATUS_GRANTED;
                break;
            case ApplicationEntityService::APPLICATION_STATUS_WITHDRAWN:
            case ApplicationEntityService::APPLICATION_STATUS_REFUSED:
                $licenceStatus = LicenceEntityService::LICENCE_STATUS_UNDER_CONSIDERATION;
                $applicationStatus = ApplicationEntityService::APPLICATION_STATUS_UNDER_CONSIDERATION;
                break;
        }

        if (!(boolean)$params['application']['isVariation']) {
            $this->getServiceLocator()
                ->get('Entity\Licence')
                ->setLicenceStatus(
                    $applicationEntityService->getLicenceIdForApplication($params['application']['id']),
                    $licenceStatus
                );
        }

        $applicationEntityService
            ->forceUpdate(
                $params['application']['id'],
                array(
                    'status' => $applicationStatus
                )
            );

        $response->setType(Response::TYPE_SUCCESS);
        return $response;
    }
}

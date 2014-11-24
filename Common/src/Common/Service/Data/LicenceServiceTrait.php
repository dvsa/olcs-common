<?php

/**
 * Licence Service Trait
 */
namespace Common\Service\Data;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Licence Service Trait
 */
trait LicenceServiceTrait
{
    /**
     * @var \Common\Service\Data\Licence
     */
    protected $licenceService;

    /**
     * @param \Common\Service\Data\Licence $licenceService
     * @return $this
     */
    public function setLicenceService($licenceService)
    {
        $this->licenceService = $licenceService;
        return $this;
    }

    /**
     * @return \Common\Service\Data\Licence
     */
    public function getLicenceService()
    {
        return $this->licenceService;
    }

    /**
     * Get Licence Ni/Goods/Psv information
     *
     * @return array
     */
    protected function getLicenceContext()
    {
        $licence = $this->getLicenceService()->fetchLicenceData();

        return [
            'isNi' => (int) ($licence['niFlag'] == 'Y'),
            'goodsOrPsv' => $licence['goodsOrPsv']['id'],
            'trafficArea' => $licence['trafficArea']['id']
        ];
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->setLicenceService($serviceLocator->get('\Common\Service\Data\Licence'));

        return $this;
    }
}

<?php

/**
 * OperatingCentreTrafficAreaValidator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * OperatingCentreTrafficAreaValidator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class OperatingCentreTrafficAreaValidator extends AbstractValidator implements ServiceLocatorAwareInterface
{

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'required' => 'Value is required and can\'t be empty',
        'notInNorthernIreland' => 'Your Operating Centre must be located in Northern Ireland',
        'notInTrafficArea' => 'Not in traffic area'
    );

    /**
     * Flag for NI application
     *
     * @var bool
     */
    private $niFlag;

    /**
     * Flag for NI application
     *
     * @var int
     */
    private $operatingCentresCount;

    /**
     * Current traffic area
     *
     * @var array
     */
    private $trafficArea;

    /**
     * Northern Ireland Traffic Area Code
     */
    const NORTHERN_IRELAND_TRAFFIC_AREA_CODE = 'N';

    /**
     * Custom validation for postcode / traffic area
     *
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        $postcodeService = $this->getServiceLocator()->get('postcode');
        if ($value) {

            list($trafficAreaId, $trafficAreaName) = $postcodeService->getTrafficAreaByPostcode($value);

            $currentTrafficArea = $this->getTrafficArea();
            $currentTrafficAreaId = is_array($currentTrafficArea) && array_key_exists('id', $currentTrafficArea) ?
                $currentTrafficArea['id'] : null;

            $niFlag = $this->getNiFlag();

            // validate only if postcode is not empty and recognized
            if ($value && $trafficAreaId) {
                if ($niFlag == 'Y' && $trafficAreaId !== self::NORTHERN_IRELAND_TRAFFIC_AREA_CODE) {
                    $this->error('notInNorthernIreland');
                    return false;
                }
                if ($niFlag == 'N' && $trafficAreaId !== $currentTrafficAreaId && $currentTrafficAreaId) {
                    $errorText = 'Your operating centre is in ' . $trafficAreaName . ' traffic area, '
                    . 'which differs to your first operating centre ' . $currentTrafficArea['name'].
                    '. You will need to apply for '
                    . 'more than one licence. Read more.';
                    $this->setMessage($errorText, 'notInTrafficArea');
                    $this->error('notInTrafficArea');
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Sets NI flag
     *
     * @param bool $niFlag
     */
    public function setNiFlag($niFlag)
    {
        $this->niFlag = $niFlag;
    }

    /**
     * Gets NI flag
     *
     * @return bool
     */
    public function getNiFlag()
    {
        return $this->niFlag;
    }

    /**
     * Sets operating centres count
     *
     * @param int $operatingCentresCount
     */
    public function setOperatingCentresCount($operatingCentresCount)
    {
        $this->operatingCentresCount = $operatingCentresCount;
    }

    /**
     * Gets operating centres count
     *
     * @return int
     */
    public function getOperatingCentresCount()
    {
        return $this->operatingCentresCount;
    }

    /**
     * Sets trafficArea
     *
     * @param string $trafficArea
     */
    public function setTrafficArea($trafficArea)
    {
        $this->trafficArea = $trafficArea;
    }

    /**
     * Gets trafficArea
     *
     * @return array
     */
    public function getTrafficArea()
    {
        return $this->trafficArea;
    }

    /**
     * Set service locator
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}

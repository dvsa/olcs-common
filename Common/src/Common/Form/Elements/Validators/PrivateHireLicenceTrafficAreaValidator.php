<?php

/**
 * PrivateHireLicenceTrafficAreaValidator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Form\Elements\Validators;

use Zend\Validator\AbstractValidator;
use Zend\ServiceManager\ServiceLocatorAwareTrait as ZendServiceLocatorAwareTrait;

/**
 * PrivateHireLicenceTrafficAreaValidator
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PrivateHireLicenceTrafficAreaValidator extends AbstractValidator
{
    use ZendServiceLocatorAwareTrait;

    /**
     * Message templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        'required' => 'Value is required and can\'t be empty',
        'notInTrafficArea' => 'Not in traffic area'
    );

    /**
     * Private hire licences count
     *
     * @var int
     */
    private $privateHireLicencesCount;

    /**
     * Current traffic area
     *
     * @var array
     */
    private $trafficArea;

    /**
     * Custom validation for postcode / traffic area
     *
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        if ($value) {

            $postcodeService = $this->getServiceLocator()->get('postcode');

            list($trafficAreaId, $trafficAreaName) = $postcodeService->getTrafficAreaByPostcode($value);

            $currentTrafficArea = $this->getTrafficArea();
            $currentTrafficAreaId = is_array($currentTrafficArea) && array_key_exists('id', $currentTrafficArea) ?
                $currentTrafficArea['id'] : null;

            // validate only if postcode is not empty and recognized
            if ($value && $trafficAreaId && $trafficAreaId !== $currentTrafficAreaId && $currentTrafficAreaId) {
                $errorText = 'Your Taxi/PHV licence is in ' . $trafficAreaName . ' traffic area, which differs '
                . 'to your first Taxi/PHV Licence (' . $currentTrafficArea['name'] . '). You will need to apply '
                . 'for more than one Special Restricted licence. Read more.';
                $this->setMessage($errorText, 'notInTrafficArea');
                $this->error('notInTrafficArea');
                return false;
            }
        }

        return true;
    }

    /**
     * Sets private hire licences count
     *
     * @param int $privateHireLicencesCount
     */
    public function setPrivateHireLicencesCount($privateHireLicencesCount)
    {
        $this->privateHireLicencesCount = $privateHireLicencesCount;
    }

    /**
     * Gets private hire licences count
     *
     * @return int
     */
    public function getPrivateHireLicencesCount()
    {
        return $this->privateHireLicencesCount;
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
}

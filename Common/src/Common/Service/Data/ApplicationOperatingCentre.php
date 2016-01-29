<?php

namespace Common\Service\Data;

use Zend\ServiceManager\FactoryInterface;

/**
 * Class ApplicationOperatingCentre
 * @package Olcs\Service
 */
class ApplicationOperatingCentre extends AbstractData implements FactoryInterface, ListDataInterface
{
    use ApplicationServiceTrait;

    const OUTPUT_TYPE_FULL = 1;
    const OUTPUT_TYPE_PARTIAL = 2;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var int
     */
    protected $outputType = self::OUTPUT_TYPE_FULL;

    /**
     * @var string
     */
    protected $serviceName = 'ApplicationOperatingCentre';

    /**
     * @param integer|null $id
     * @param array|null $bundle
     * @return array
     */
    public function fetchListOptions($context = null, $useGroups = false)
    {
        $id = $this->getId();
        if (is_null($this->getData($id))) {
            $data = array();
            $rawData =  $this->getApplicationService()->fetchOperatingCentreData($this->getId(), $this->getBundle());

            if (is_array($rawData['operatingCentres'])) {
                $outputType = $this->getOutputType();
                foreach ($rawData['operatingCentres'] as $applicationOperatingCentre) {
                    if ($outputType == self::OUTPUT_TYPE_PARTIAL) {
                        $fields = [
                            'addressLine1',
                            'town'
                        ];
                    } else {
                        $fields = [
                            'addressLine1',
                            'addressLine2',
                            'addressLine3',
                            'addressLine4',
                            'town',
                            'postcode',
                        ];
                    }
                    $addressString = '';
                    foreach ($fields as $field) {
                        $addressString .= !empty($applicationOperatingCentre['operatingCentre']['address'][$field]) ?
                            $applicationOperatingCentre['operatingCentre']['address'][$field] . ', ' : '';
                    }
                    $data[$applicationOperatingCentre['operatingCentre']['id']] = substr($addressString, 0, -2);
                }
            }
            $this->setData($id, $data);
        }
        return $this->getData($id);
    }


    /**
     * @return array
     */
    public function getBundle()
    {
        $bundle = array(
            'children' => array(
                'operatingCentres' => array(
                    'children' => array(
                        'operatingCentre' => array(
                            'children' => array(
                                'address'
                            )
                        )
                    )
                )
            )
        );

        return $bundle;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->getApplicationService()->getId();
    }

    /**
     * @return int
     */
    public function getOutputType()
    {
        return $this->outputType;
    }

    /**
     * @param int $outputType
     */
    public function setOutputType($outputType)
    {
        $this->outputType = $outputType;
    }
}

<?php

namespace Common\Service\Data;

/**
 * Class Licence
 * @package Olcs\Service
 */
class Licence extends AbstractData implements AddressProviderInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $serviceName = 'Licence';

    /**
     * @param integer|null $id
     * @param array|null $bundle
     * @return array
     */
    public function fetchLicenceData($id = null, $bundle = null)
    {
        $id = is_null($id) ? $this->getId() : $id;

        if (is_null($this->getData($id))) {
            $bundle = is_null($bundle) ? $this->getBundle() : $bundle;
            $data =  $this->getRestClient()->get(sprintf('/%d', $id), ['bundle' => json_encode($bundle)]);
            $this->setData($id, $data);
        }
        return $this->getData($id);
    }

    /**
     * Fetches an array of addresses for the licence. Queries the ContactDetails table
     * @param null $id
     * @param null $bundle
     * @return array
     */
    public function fetchAddressListData($id = null, $bundle = null)
    {
        $id = is_null($id) ? $this->getId() : $id;

        if (is_null($this->getData('addr_' .$id))) {
            $data = array();
            $bundle = is_null($bundle) ? $this->getAddressBundle() : $bundle;
            $addressData =  $this->getRestClient()->get(sprintf('/%d', $id), ['bundle' => json_encode($bundle)]);

            if (isset($addressData['correspondenceCd']['address'])) {
                $data[] = $addressData['correspondenceCd']['address'];
            }
            if (isset($addressData['establishmentCd']['address'])) {
                $data[] = $addressData['establishmentCd']['address'];
            }
            if (isset($addressData['transportConsultantCd']['address'])) {
                $data[] = $addressData['transportConsultantCd']['address'];
            }

            $this->setData('addr_' .$id, $data);
        }
        return $this->getData('addr_' .$id);
    }

    /**
     * Bundle to fetch all addresses for licence
     * @return array
     */
    public function getAddressBundle()
    {
        $bundle = array(
            'children' => array(
                'correspondenceCd' => array(
                    'properties' => 'ALL',
                    'children' => array(
                        'address' => array(
                            'properties' => 'ALL'
                        )
                    )
                ),
                'establishmentCd' => array(
                    'properties' => 'ALL',
                    'children' => array(
                        'address' => array(
                            'properties' => 'ALL'
                        )
                    )
                ),
                'transportConsultantCd' => array(
                    'properties' => 'ALL',
                    'children' => array(
                        'address' => array(
                            'properties' => 'ALL'
                        )
                    )
                )
            )
        );

        return $bundle;
    }

    /**
     * @return array
     */
    public function getBundle()
    {
        $bundle = array(
            'properties' => 'ALL',
            'children' => array(
                'cases' => array(
                    'properties' => 'ALL',
                    'children' => array(
                        'appeals' => array(
                            'properties' => 'ALL',
                            'children' => array(
                                'outcome' => array(
                                    'properties' => array(
                                        'id',
                                        'description'
                                    )
                                ),
                                'reason' => array(
                                    'properties' => array(
                                        'id',
                                        'description'
                                    )
                                ),
                            )
                        ),
                        'stays' => array(
                            'properties' => 'ALL',
                            'children' => array(
                                'stayType' => array(
                                    'properties' => array(
                                        'id',
                                        'description'
                                    )
                                ),
                                'outcome' => array(
                                    'properties' => array(
                                        'id',
                                        'description'
                                    )
                                )
                            )
                        )
                    )
                ),
                'status' => array(
                    'properties' => array('id', 'description')
                ),
                'goodsOrPsv' => array(
                    'properties' => array('id', 'description')
                ),
                'licenceType' => array(
                    'properties' => 'ALL'
                ),
                'trafficArea' => array(
                    'properties' => 'ALL'
                ),
                'organisation' => array(
                    'properties' => 'ALL',
                    'children' => array(
                        'organisationPersons' => array(
                            'properties' => 'ALL'
                        ),
                        'tradingNames' => array(
                            'properties' => 'ALL'
                        )
                    )
                )
            )
        );

        return $bundle;
    }

    /**
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}

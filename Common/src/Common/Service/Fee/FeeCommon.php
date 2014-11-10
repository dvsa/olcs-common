<?php
/**
 * Fee Common Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Service\Fee;

use Common\Service\Fee\Exception as FeeException;
use Common\Util\RestCallTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * Fee Common Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class FeeCommon implements ServiceLocatorAwareInterface, FactoryInterface
{
    use RestCallTrait,
        ServiceLocatorAwareTrait;

    const FEE_TYPE_APPLICATION = 'APP';

    const NI_TA_CODE = 'N';

    const CURRENT_USER_ID = 2;

    /**
     * Generate fee
     *
     * @param string $type
     * @param int $applicationId
     */
    public function generateFee($type = self::FEE_TYPE_APPLICATION, $applicationId = null)
    {
        switch ($type) {
            case self::FEE_TYPE_APPLICATION:
                $this->generateApplicationFee($applicationId);
                break;
            default:
                throw new FeeException('Not implemented');
        }
    }

    /**
     * Generate application fee
     *
     * @param int $applicationId
     */
    protected function generateApplicationFee($applicationId = null)
    {
        if (!$applicationId) {
            throw new FeeException('Please provide application id');
        }

        $applicationDetails = $this->getApplicationDetails($applicationId);
        $feeType = $this->getApplicationFeeType(
            $applicationDetails['goodsOrPsv'],
            $applicationDetails['licenceType'],
            $applicationDetails['niFlag'],
            $applicationDetails['applicationDate']
        );
        $params = [
            'amount' => (float) $feeType['fixedValue'] ? $feeType['fixedValue'] : $feeType['fiveYearValue'],
            'application' => $applicationId,
            'licence' => $applicationDetails['licenceId'],
            'invoicedDate' => date('Y-m-d'),
            'feeType' => $feeType['id'],
            'description' => $feeType['description'] . ' for application ' . $applicationId,
            'feeStatus' => 'lfs_ot',
            // @TODO: change to real user id when implemented
            'createdBy' => self::CURRENT_USER_ID,
            // @TODO: change to real user id when implemented
            'lastModifiedBy' => self::CURRENT_USER_ID,
            'lastModifiedOn' => date('Y-m-d H:s:i')
        ];

        $this->makeRestCall('Fee', 'POST', $params);

    }

    /**
     * Get application fee type
     *
     * @param string $licenceCategory
     * @param string $licenceType
     * @param string $niFlag
     * @param string $applicationDate
     * @return array
     */
    protected function getApplicationFeeType($operatorType, $licenceType, $niFlag, $applicationDate)
    {
        $bundle = [
            'properties' => [
                'id',
                'description',
                'fixedValue',
                'fiveYearValue',
                'effectiveFrom'
            ],
            'children' => [
                'trafficArea' => [
                    'properties' => [
                        'id'
                    ]
                ],
                'licenceType' => [
                    'properties' => [
                        'id'
                    ]
                ],
                'goodsOrPsv' => [
                    'properties' => [
                        'id'
                    ]
                ]
            ]
        ];
        $params = [
            'goodsOrPsv' => $operatorType,
            'feeType' => self::FEE_TYPE_APPLICATION,
            'effectiveFrom' => '<= ' . $applicationDate,
            'sort' => 'effectiveFrom',
            'order' => 'DESC'
        ];
        $data = $this->makeRestCall('FeeType', 'GET', $params, $bundle);

        // filter results by licence type && niFlag

        $results = [];

        if (is_array($data['Results']) && count($data['Results'])) {
            foreach ($data['Results'] as $result) {
                if (
                    (
                        !count($result['licenceType']) ||
                        (isset($result['licenceType']['id']) && $result['licenceType']['id'] == $licenceType)
                    )
                    &&
                    (
                       ($niFlag == 'Y' && isset($result['trafficArea']['id'])
                            && $result['trafficArea']['id'] == self::NI_TA_CODE) ||
                       ($niFlag == 'N'
                            && (!isset($result['trafficArea']['id'])
                                || $result['trafficArea']['id'] != self::NI_TA_CODE))
                    )
                   ) {
                    $results[] = $result;
                }
            }
        }
        if (!count($results)) {
            throw new Exception('No fee type found for the new application');
        }

        return $results[0];
    }

    /**
     * Get application details
     *
     * @param int $applicationId
     * @return array
     */
    protected function getApplicationDetails($applicationId)
    {
        $bundle = [
            'properties' => [
                'receivedDate',
                'createdOn'
            ],
            'children' => [
                'licence' => [
                    'properties' => [
                        'id',
                        'niFlag'
                    ],
                    'children' => [
                        'licenceType' => [
                            'properties' => [
                                'id'
                            ]
                        ],
                        'goodsOrPsv' => [
                            'properties' => [
                                'id'
                            ]
                        ]
                    ]
                ]
            ]

        ];
        $data = $this->makeRestCall('Application', 'GET', $applicationId, $bundle);
        $retv = [
            'id' => $data['licence']['id'],
            'niFlag' => $data['licence']['niFlag'],
            'licenceType' => $data['licence']['licenceType']['id'],
            'goodsOrPsv' => $data['licence']['goodsOrPsv']['id'],
            'applicationDate' => (isset($data['receivedDate']) && $data['receivedDate']) ?
                $data['receivedDate'] : $data['createdOn'],
            'licenceId' => $data['licence']['id']
        ];
        return $retv;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);

        return $this;
    }
}

<?php

/**
 * Fee Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

use Common\RefData;
use Common\Service\Data\FeeTypeDataService;
use Common\Service\Entity\LicenceEntityService as Licence;
use Common\Service\Entity\ApplicationEntityService as Application;

/**
 * Fee Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FeeEntityService extends AbstractLvaEntityService
{
    /**
     * Define entity for default behaviour
     *
     * @var string
     */
    protected $entity = 'Fee';

    protected $applicationIdBundle = array(
        'children' => array(
            'application' => array(
                'children' => array(
                    'status'
                )
            )
        )
    );

    /**
     * Holds the overview bundle
     *
     * @var array
     */
    private $overviewBundle = array(
        'children' => array(
            'feeStatus',
            'feePayments' => array(
                'children' => array(
                    'payment' => array(
                        'children' => array(
                            'status'
                        )
                    )
                )
            ),
            'paymentMethod',
            'feeType' => array(
                'children' => array(
                    'feeType', // need this now that fee_type.fee_type is ref_data!
                ),
            ),
        )
    );

    /**
     * @var array
     */
    protected $organisationBundle = array(
        'children' => array(
            'licence' => array(
                'children' => array(
                    'organisation'
                )
            )
        )
    );

    protected $latestFeeByTypeStatusesAndApplicationBundle = array(
        'children' => array(
            'feeType' => array(
                'children' => array('accrualRule' => array())
            ),
            'feePayments' => array(
                'children' => array(
                    'payment' => array(
                        'children' => array(
                            'status'
                        )
                    )
                )
            ),
            'paymentMethod',
        )
    );

    protected $outstandingForOrganisationBundle = array(
        'children' => array(
            'feeStatus',
            'feePayments' => array(
                'children' => array(
                    'payment' => array(
                        'children' => array(
                            'status'
                        )
                    )
                )
            ),
            'paymentMethod',
            'feeType' => array(
                'children' => array(
                    'feeType',
                ),
            ),
            'licence',
        )
    );

    /**
     * Holds the interim details bundle
     *
     * @var array
     */
    private $interimDetailsBundle = array(
        'children' => array(
            'feeType' => array(
                'children' => array(
                    'feeType'
                ),
            ),
            'application' => array(
                'children' => array(
                    'interimStatus'
                )
            )
        )
    );

    public function getApplication($id)
    {
        $data = $this->get($id, $this->applicationIdBundle);

        return isset($data['application']) ? $data['application'] : null;
    }

    public function getLatestFeeForBusReg($busRegId)
    {
        $params = [
            'busReg' => $busRegId,
            'sort'  => 'invoicedDate',
            'order' => 'DESC',
            'limit' => 1,
        ];

        $data = $this->get($params, $this->overviewBundle);

        return !empty($data['Results']) ? $data['Results'][0] : null;
    }

    /**
     * Get data for overview
     *
     * @param int $id
     * @return array
     */
    public function getOverview($id)
    {
        return $this->get($id, $this->overviewBundle);
    }

    public function getOrganisation($id)
    {
        $data = $this->get($id, $this->organisationBundle);

        return isset($data['licence']['organisation']) ? $data['licence']['organisation'] : null;
    }

    /**
     * Get fee by type, statuses and application if
     *
     * @param int $feeType
     * @param array $feeStatuses
     * @param int $applicationId
     * @return array
     */
    public function getFeeByTypeStatusesAndApplicationId($feeType, $feeStatuses, $applicationId)
    {
        $query = array(
            'application' => $applicationId,
            'feeStatus' => $feeStatuses,
            'feeType' => $feeType,
            'sort'  => 'createdOn',
            'order' => 'DESC',
        );
        return $this->getAll($query)['Results'];
    }

    /**
     * Get latest fee by type, statuses and application id
     *
     * @param int $feeType
     * @param array $feeStatuses
     * @param int $applicationId
     * @return array fee
     */
    public function getLatestFeeByTypeStatusesAndApplicationId($feeType, $feeStatuses, $applicationId)
    {
        $query = array(
            'application' => $applicationId,
            'feeStatus' => $feeStatuses,
            'feeType' => $feeType,
            'sort'  => 'invoicedDate',
            'order' => 'DESC',
            'limit' => 1,
        );
        $data = $this->get($query, $this->latestFeeByTypeStatusesAndApplicationBundle);
        return !empty($data['Results']) ? $data['Results'][0] : null;
    }

    /**
     * Cancel fee by ids
     *
     * @param array $ids
     */
    public function cancelByIds($ids)
    {
        $updates = array();

        foreach ($ids as $id) {
            $updates[] = array(
                'id' => $id,
                'feeStatus' => RefData::FEE_STATUS_CANCELLED,
                '_OPTIONS_' => array('force' => true)
            );
        }
        $this->multiUpdate($updates);
    }

    /**
     * Get any outstanding continuation fees for a licence
     *
     * @param int $licenceId
     *
     * @return array Entity data ['Count' => x, 'Results' => [y]]
     */
    public function getOutstandingContinuationFee($licenceId)
    {
        $query = [
            'licence' => $licenceId,
            'feeStatus' => RefData::FEE_STATUS_OUTSTANDING,
        ];
        $bundle = [
            'children' => [
                'feeType' => [
                    'criteria' => [
                        'feeType' => FeeTypeDataService::FEE_TYPE_CONT,
                    ],
                    'required' => true,
                ]
            ]
        ];
        return $this->getAll($query, $bundle);
    }

    /**
     * Get fee details to check if we need grant interim
     *
     * @param int $feeId
     * @return array
     */
    public function getFeeDetailsForInterim($feeId)
    {
        return $this->get($feeId, $this->interimDetailsBundle);
    }
}

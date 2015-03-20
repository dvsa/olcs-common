<?php

/**
 * Fee Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

use Common\Service\Data\FeeTypeDataService;

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

    const STATUS_OUTSTANDING = 'lfs_ot';
    const STATUS_PAID = 'lfs_pd';
    const STATUS_WAIVE_RECOMMENDED = 'lfs_wr';
    const STATUS_WAIVED = 'lfs_w';
    const STATUS_CANCELLED = 'lfs_cn';

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
            'feeType'
        )
    );

    /**
     * @var array
     */
    private $latestOutstandingFeeForBundle = array(
        'children' => array(
            'application',
            'licence',
            'feeType' => array(
                'properties' => 'id',
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

    public function getApplication($id)
    {
        $data = $this->get($id, $this->applicationIdBundle);

        return isset($data['application']) ? $data['application'] : null;
    }

    public function getOutstandingFeesForApplication($applicationId)
    {
        $query = array(
            'application' => $applicationId,
            'feeStatus' => array(
                self::STATUS_OUTSTANDING,
                self::STATUS_WAIVE_RECOMMENDED
            )
        );

        $data = $this->getAll($query, $this->overviewBundle);

        return $data['Results'];
    }

    public function getLatestOutstandingFeeForApplication($applicationId)
    {
        $params = [
            'application' => $applicationId,
            'feeStatus' => array(
                self::STATUS_OUTSTANDING,
                self::STATUS_WAIVE_RECOMMENDED
            ),
            'sort'  => 'invoicedDate',
            'order' => 'DESC',
            'limit' => 1
        ];

        $data = $this->get($params, $this->latestOutstandingFeeForBundle);

        return !empty($data['Results']) ? $data['Results'][0] : null;
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

    public function cancelForLicence($licenceId)
    {
        $query = array(
            'licence' => $licenceId,
            'feeStatus' => array(
                self::STATUS_OUTSTANDING,
                self::STATUS_WAIVE_RECOMMENDED
            )
        );

        $results = $this->getAll($query, array('children' => array('task')));

        if (empty($results['Results'])) {
            return;
        }

        $updates = array();
        $tasks = array();

        foreach ($results['Results'] as $fee) {
            $updates[] = array(
                'id' => $fee['id'],
                'feeStatus' => self::STATUS_CANCELLED,
                '_OPTIONS_' => array('force' => true)
            );
            if (isset($fee['task']['id'])) {
                $tasks[] = array(
                    'id' => $fee['task']['id'],
                    'version' => $fee['task']['version'],
                    'isClosed' => 'Y'
                );
            }
        }

        $updates['_OPTIONS_']['multiple'] = true;
        $this->put($updates);
        if ($tasks) {
            $this->getServiceLocator()->get('Entity\Task')->multiUpdate($tasks);
        }
    }

    public function cancelForApplication($applicationId)
    {
        $query = array(
            'application' => $applicationId,
            'feeStatus' => array(
                self::STATUS_OUTSTANDING,
                self::STATUS_WAIVE_RECOMMENDED
            )
        );

        $results = $this->getAll($query, array('properties' => array('id')));

        if (empty($results['Results'])) {
            return;
        }

        $updates = array();

        foreach ($results['Results'] as $fee) {
            $updates[] = array(
                'id' => $fee['id'],
                'feeStatus' => self::STATUS_CANCELLED,
                '_OPTIONS_' => array('force' => true)
            );
        }

        $updates['_OPTIONS_']['multiple'] = true;

        $this->put($updates);
    }

    public function cancelInterimForApplication($applicationId)
    {
        $results = $this->getOutstandingFeesForApplication($applicationId);

        $updates = [];
        foreach ($results as $fee) {
            if ($fee['feeType']['feeType'] === FeeTypeDataService::FEE_TYPE_GRANTINT) {
                $updates[] = [
                    'id' => $fee['id'],
                    'feeStatus' => self::STATUS_CANCELLED,
                    '_OPTIONS_' => array('force' => true)
                ];
            }
        }

        $updates['_OPTIONS_']['multiple'] = true;

        $this->put($updates);
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
            'feeType' => $feeType
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
        $data = $this->get($query);
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
                'feeStatus' => self::STATUS_CANCELLED,
                '_OPTIONS_' => array('force' => true)
            );
        }
        $this->multiUpdate($updates);
    }
}

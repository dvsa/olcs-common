<?php

/**
 * Fee Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

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
            )
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

        $data = $this->getAll($query, array('properties' => array('id')));

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
            'limit' => 1,
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
}

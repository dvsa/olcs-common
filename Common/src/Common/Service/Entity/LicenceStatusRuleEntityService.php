<?php

/**
 * LicenceStatusRuleEntityService.php
 */
namespace Common\Service\Entity;

/**
 * Class LicenceStatusRuleEntityService
 *
 * Entity service for the Licence Status entity, this class provides (amongst other things)
 * create, read, update and delete for the Licence Status entity.
 *
 * @package Common\Service\Entity
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class LicenceStatusRuleEntityService extends AbstractEntityService
{
    const LICENCE_STATUS_RULE_CURTAILED = 'lsts_curtailed';
    const LICENCE_STATUS_RULE_REVOKED = 'lsts_revoked';
    const LICENCE_STATUS_RULE_SUSPENDED = 'lsts_suspended';

    /**
     * The entity reference.
     *
     * @var string
     */
    protected $entity = 'LicenceStatusRule';

    /**
     * Argument defaults.
     *
     * @var array
     */
    protected $argumentDefaults = array(
        'data' => array(
            'licenceStatus' => null,
            'startDate' => null,
            'endDate' => null,
            'startProcessedDate' => null,
            'endProcessedDate' => null,
        ),
        'query' => array(
            'licenceStatus' => array()
        )
    );

    /**
     * Create a new licence status for a licence.
     *
     * @param null $licenceId The licence id.
     * @param array $args The arguments
     *
     * @throws \Common\Exception\ConfigurationException
     */
    public function createStatusForLicence($licenceId = null, array $args = array())
    {
        $args = $this->normaliseDataArguments($licenceId, $args);

        return $this->save($args);
    }

    /**
     * Get all licence statuses by a set of criteria.
     *
     * @param null $licenceId The licence id.
     * @param array $args The arguments
     *
     * @return array
     */
    public function getStatusesForLicence($licenceId = null, array $args = array())
    {
        $args = $this->normaliseQueryArguments($licenceId, $args);

        return $this->getList($args);
    }

    public function updateStatusesForLicence($licenceId = null, array $args = array())
    {
        $args = $this->normaliseArguments($args);
    }

    /**
     * Set the deleted date on licence status record.
     *
     * @param null $licenceStatusId The licence status record id.
     */
    public function removeStatusesForLicence($licenceStatusId = null)
    {
        $this->delete($licenceStatusId);
    }

    /**
     * Normalise data arguments specifically.
     *
     * @param $licenceId The licence id.
     * @param array $args The data arguments.
     *
     * @return array
     */
    private function normaliseDataArguments($licenceId, array $args = array())
    {
        $args['data']['licence'] = $licenceId;

        return array_merge($this->argumentDefaults['data'], $args['data']);
    }

    /**
     * Normalise query arguments specifically.
     *
     * @param $licenceId The licence id.
     * @param array $args The query arguments.
     *
     * @return array
     */
    private function normaliseQueryArguments($licenceId, array $args = array())
    {
        return array_merge($this->argumentDefaults['query'], $args);
    }

    /**
     * Get Licence rules to be actioned for revocation, curtailment and suspension
     *
     * @return array
     */
    public function getLicencesToRevokeCurtailSuspend()
    {
        $query = [
            'startProcessedDate' => 'NULL',
            'deletedDate' => 'NULL',
            'startDate' => '<='. $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s'),
        ];
        $bundle = [
            'children' => [
                'licenceStatus',
                'licence' => [
                    'children' => ['status']
                ],

            ],
        ];

        return $this->getAll($query, $bundle)['Results'];
    }

    /**
     * Get Licence rules to reset licence status back to vaild
     *
     * @return array
     */
    public function getLicencesToValid()
    {
        $query = [
            'endProcessedDate' => 'NULL',
            'endDate' => 'NOT NULL',
            'deletedDate' => 'NULL',
            'endDate' => '<='. $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s'),
        ];
        $bundle = [
            'children' => [
                'licenceStatus',
                'licence' => [
                    'children' => [
                        'status',
                        'licenceVehicles' => [
                            'children' => ['vehicle']
                        ]
                    ]
                ],

            ],
        ];

        return $this->getAll($query, $bundle)['Results'];
    }
}

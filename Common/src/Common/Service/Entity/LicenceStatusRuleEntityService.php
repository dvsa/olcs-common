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
 *
 * @todo possible have one delagative configuration method.
 * @todo complete commenting.
 */
class LicenceStatusRuleEntityService extends AbstractEntityService
{
    const LICENCE_STATUS_RULE_CURTAILED = 'lsts_curtailed';
    const LICENCE_STATUS_RULE_REVOKED = 'lsts_revoked';
    const LICENCE_STATUS_RULE_SUSPENDED = 'lsts_suspended';

    protected $entity = 'LicenceStatusRule';

    private $licenceStatus = array(
        'CURT' => self::LICENCE_STATUS_RULE_CURTAILED,
        'REVO' => self::LICENCE_STATUS_RULE_REVOKED,
        'SUSP' => self::LICENCE_STATUS_RULE_SUSPENDED
    );

    protected $argumentDefaults = array(
        'code' => null,
        'data' => array(
            'startDate' => null,
            'endDate' => null,
            'startProcessedDate' => null,
            'endProcessedDate' => null,
        ),
        'query' => array()
    );

    /**
     * Create a new licence status for a licence.
     *
     * @param null $licenceId The licence id.
     * @param array $args The arguments
     *
     * @throws \Common\Exception\ConfigurationException
     */
    public function createStatusForLicence($licenceId = null, array $args)
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
    public function getStatusesForLicence($licenceId = null, array $args)
    {
        $args = $this->normaliseQueryArguments($licenceId, $args);

        return $this->getList($args);
    }

    public function updateStatusesForLicence($licenceId = null, array $args)
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
     * Get the full status code from the short code.
     *
     * @param null $statusShortCode The status short code.
     *
     * @throws \InvalidArgumentException If the short code cannot be mapped.
     *
     * @return string The full status code.
     */
    protected function getLicenceStatusFromShortCode($statusShortCode = null)
    {
        if (is_null($statusShortCode) || !isset($this->licenceStatus[$statusShortCode])) {
            throw new \InvalidArgumentException(__METHOD__ . ' shortcode not recognised.');
        }

        return $this->licenceStatus[$statusShortCode];
    }

    /**
     * Normalise data arguments specifically.
     *
     * @param $licenceId The licence id.
     * @param array $args The data arguments.
     *
     * @return array
     */
    private function normaliseDataArguments($licenceId, array $args)
    {
        $args['data']['licence'] = $licenceId;
        $args['data']['licenceStatus'] = $this->getLicenceStatusFromShortCode($args['code']);

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
    private function normaliseQueryArguments($licenceId, array $args)
    {
        $args['query']['code'] = $this->getLicenceStatusFromShortCode($args['code']);

        return array_merge($this->argumentDefaults['query'], $args['query']);
    }
}
<?php

namespace Common\Service\Entity;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;

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
        'data' => array(),
        'query' => array()
    );

    public function createStatusForLicence($licenceId = null, array $args)
    {
        $args = $this->normaliseArguments($args);
    }

    public function getStatusesForLicence($licenceId = null, array $args)
    {
        $args = $this->normaliseArguments($args);
    }

    public function updateStatusesForLicence($licenceId = null, array $args)
    {
        $args = $this->normaliseArguments($args);
    }

    public function removeStatusesForLicence($licenceId = null, array $args)
    {
        $args = $this->normaliseArguments($args);
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
     * Normalise the method arguments passed by merging them with the defaults.
     *
     * @param array $args Array of arguments.
     *
     * @return array Merged argument array.
     */
    private function normaliseArguments(array $args)
    {
        return array_merge($args, $this->argumentDefaults);
    }
}
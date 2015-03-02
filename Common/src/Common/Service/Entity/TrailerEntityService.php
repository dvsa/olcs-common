<?php

/**
 * TrailerEntityService.php
 */

namespace Common\Service\Entity;

/**
 * Class TrailerEntityService
 *
 * Facilitates communication between the API and the business layer.
 * This service deals only with the trailers endpoint.
 *
 * @package Common\Service\Entity
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class TrailerEntityService extends AbstractLvaEntityService
{
    /**
     * The entity services entity.
     *
     * @var string $entity The entity.
     */
    protected $entity = "Trailer";

    /**
     * Get all trailers for a licence.
     *
     * @param null|string|int $licenceId The licence identifier.
     *
     * @throws \InvalidArgumentException If no licence ID is passed.
     *
     * @return array The results.
     */
    public function getTrailerDataForLicence($licenceId = null)
    {
        if (is_null($licenceId)) {
            throw new \InvalidArgumentException(
                __METHOD__ . " expects a licence ID."
            );
        }

        return $this->get(['licence' => $licenceId]);
    }
}
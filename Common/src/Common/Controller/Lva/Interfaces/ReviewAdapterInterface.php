<?php

/**
 * Review Adapter Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Interfaces;

/**
 * Review Adapter Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface ReviewAdapterInterface
{
    /**
     * Get all sections for a given application id
     *
     * @param int $id
     * @param array $relevantSections
     * @return array
     */
    public function getSectionData($id, array $relevantSections = array());
}

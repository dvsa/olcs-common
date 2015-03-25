<?php

/**
 * Community Licence Adapter Interface
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Lva\Interfaces;

/**
 * Community Licence Adapter Interface
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
interface CommunityLicenceAdapterInterface extends AdapterInterface
{
    /**
     * Add office copy
     */
    public function addOfficeCopy($licenceId, $identifier);

    /**
     * Get total authority
     */
    public function getTotalAuthority($id);

    /**
     * Add community licences
     */
    public function addCommunityLicences($licenceId, $totalLicences, $identifier);
}

<?php

/**
 * Community Licence Adapter Interface
 *
 * @author Alex Peshkov <alex.peshkov@vltech.co.uk>
 */
namespace Common\Controller\Lva\Interfaces;

/**
 * Community Licence Adapter Interface
 *
 * @author Alex Peshkov <alex.peshkov@vltech.co.uk>
 */
interface CommunityLicenceAdapterInterface extends AdapterInterface
{
    /**
     * Add office copy
     */
    public function addOfficeCopy($licenceId);

    /**
     * Get total authority
     */
    public function getTotalAuthority($id);

    /**
     * Add community licences
     */
    public function addCommunityLicences($licenceId, $totalLicences);
}

<?php

/**
 * Licence Transport Manager Adapter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

/**
 * Licence Transport Manager Adapter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class LicenceTransportManagerAdapter extends AbstractTransportManagerAdapter
{
    protected $lva = 'licence';
    protected $entityService = 'Entity\ApplicationOperatingCentre';
}

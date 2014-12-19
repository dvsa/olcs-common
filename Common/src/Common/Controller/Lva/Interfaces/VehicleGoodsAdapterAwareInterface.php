<?php

/**
 * Vehicle Goods Adapter Aware Interface
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Lva\Interfaces;

/**
 * Vehicle Goods Adapter Aware Interface
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
interface VehicleGoodsAdapterAwareInterface
{
    /**
     * @return TypeOfLicenceAdapterInterface
     */
    public function getVehicleGoodsAdapter();

    public function setVehicleGoodsAdapter(VehicleGoodsAdapterInterface $adapter);
}

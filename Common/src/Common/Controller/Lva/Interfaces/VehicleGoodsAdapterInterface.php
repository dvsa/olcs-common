<?php

/**
 * Vehicle Goods Adapter Interface
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Lva\Interfaces;

/**
 * Vehicle Goods Adapter Interface
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
interface VehicleGoodsAdapterInterface extends AdapterInterface
{
    public function populateForm($request, $entityData, $form);
}

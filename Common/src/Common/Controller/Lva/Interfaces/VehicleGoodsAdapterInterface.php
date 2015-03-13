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
interface VehicleGoodsAdapterInterface extends AdapterInterface, VehiclesAdapterInterface
{
    public function save($data, $id);

    public function getFormData($id);

    public function showFilters();

    public function getFilterForm();

    public function getFilters($params);
}

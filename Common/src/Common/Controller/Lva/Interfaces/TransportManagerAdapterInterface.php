<?php

/**
 * Transport Manager Adapter Interface
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\Controller\Lva\Interfaces;

/**
 * Transport Manager Adapter Interface
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
interface TransportManagerAdapterInterface extends AdapterInterface
{
    public function getTable();

    public function getTableData($applicationId, $licenceId);

    public function getForm();

    public function mustHaveAtLeastOneTm($lvaId);

    public function delete(array $ids, $applicationId);
}

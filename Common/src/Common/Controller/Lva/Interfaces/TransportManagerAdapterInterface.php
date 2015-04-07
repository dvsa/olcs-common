<?php

/**
 * Transport Manager Adapter Interface
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\Controller\Lva\Interfaces;

use Zend\Form\Form;
use Zend\Http\Request;

/**
 * Transport Manager Adapter Interface
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
interface TransportManagerAdapterInterface extends AdapterInterface
{
    public function getTable();

    public function getTableData($lvaId);

    public function getForm();

    public function mustHaveAtLeastOneTm($lvaId);
}

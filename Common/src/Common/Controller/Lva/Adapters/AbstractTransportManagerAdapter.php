<?php

/**
 * Abstract Transport Manager Adapter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Adapters\AbstractControllerAwareAdapter;
use Common\Controller\Lva\Interfaces\TransportManagerAdapterInterface;

/**
 * Abstract Transport Manager Adapter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
abstract class AbstractTransportManagerAdapter extends AbstractControllerAwareAdapter implements
    TransportManagerAdapterInterface
{
    /**
     * Get transport managers form
     *
     * @return \Zend\Form\Form
     */
    public function getForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\TransportManagers');
    }

    /**
     * Get the table
     *
     * @return \Common\Service\Table\TableBuilder
     */
    public function getTable($template = 'lva-transport-manangers')
    {
        return $this->getServiceLocator()->get('Table')->prepareTable($template);
    }

    /**
     * Is this licence required to have at least one Transport Manager
     *
     * @param int $lvaId The Licence, Variation or Application ID
     *
     * @return boolean
     */
    public function mustHaveAtLeastOneTm($lvaId)
    {
        return false;
    }
}

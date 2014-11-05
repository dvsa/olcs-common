<?php

/**
 * Abstract View Model
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\View;

use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Abstract View Model
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractViewModel extends ViewModel implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Build a table from config and results, and return the table object
     *
     * @param string $table
     * @param array $results
     * @param array $data
     * @return string
     */
    public function getTable($table, $results, $data = array())
    {
        if (!isset($data['url'])) {
            $data['url'] = $this->getServiceLocator()->get('Helper\Url');
        }

        return $this->getServiceLocator()->get('Table')->buildTable($table, $results, $data, false);
    }
}

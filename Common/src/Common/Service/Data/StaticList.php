<?php

namespace Common\Service\Data;

use Common\Service\Data\Interfaces\ListData;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class StaticList
 *
 * @package Common\Service\Data
 */
class StaticList implements ListData, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Fetch list options
     *
     * @param string $context   Context
     * @param bool   $useGroups Use groups
     *
     * @return array
     */
    public function fetchListOptions($context, $useGroups = false)
    {
        $data = $this->fetchListData($context);

        if (!$data) {
            return [];
        }

        return $data;
    }

    /**
     * Get static list data from config
     *
     * @param string $context Context
     *
     * @return array
     */
    public function fetchListData($context)
    {
        $config = $this->getServiceLocator()->get('Config');

        if (is_null($this->getData('static-list-' . $context))) {

            $data = isset($config['static-list-data'][$context]) ? $config['static-list-data'][$context] : [];
            $this->setData('static-list-' . $context, $data);
        }

        return $this->getData('static-list-' . $context);
    }
}

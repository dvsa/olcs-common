<?php

namespace Common\Service\Data;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class StaticList
 * @package Common\Service
 */
class StaticList extends AbstractData implements ListDataInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @param $category
     * @param bool $useGroups
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
     * @param string $context
     * @return array
     */
    public function fetchListData($context)
    {
        $config = $this->getServiceLocator()->getServiceLocator()->get('Config');
        if (is_null($this->getData('static-list-' . $context))) {

            $data = isset($config['static-list-data'][$context]) ? $config['static-list-data'][$context] : [];
            $this->setData('static-list-' . $context, $data);

        }

        return $this->getData('static-list-' . $context);
    }
}

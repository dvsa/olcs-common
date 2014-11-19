<?php

namespace Common\Filter\Publication;

use Zend\Filter\AbstractFilter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Publication filter
 * @package Common\Filter
 */
class AbstractPublicationFilter extends AbstractFilter implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Merges the new data with the existing ArrayObject
     *
     * @param \Zend\Stdlib\ArrayObject $publication
     * @return \Zend\Stdlib\ArrayObject
     */
    public function mergeData($publication, $newData)
    {
        $publication->exchangeArray(array_merge((array)$publication->getArrayCopy(), $newData));

        return $publication;
    }

    /**
     * Method should be overridden
     *
     * @param mixed $value
     * @return mixed|void
     */
    public function filter($value)
    {

    }
}
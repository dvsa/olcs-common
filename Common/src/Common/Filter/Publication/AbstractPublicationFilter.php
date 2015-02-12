<?php

/**
 * Abstract publication filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

use Zend\Filter\AbstractFilter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Abstract publication filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class AbstractPublicationFilter extends AbstractFilter implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $publicationNewStatus = 'pub_s_new';
    protected $hearingSectionId = 13;
    protected $tmHearingSectionId = 27;
    protected $decisionSectionId = 14;
    protected $tmDecisionSectionId = 28;

    /**
     * Merges the new data with the existing ArrayObject
     *
     * @param \Common\Data\Object\Publication $publication
     * @param array $newData
     * @return \Common\Data\Object\Publication
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
        return $value;
    }
}

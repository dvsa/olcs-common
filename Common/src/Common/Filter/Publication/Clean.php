<?php

/**
 * Removes extra data used to build up our object
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Removes extra data used to build up our object
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Clean extends AbstractPublicationFilter
{
    /**
     * @param \Zend\Stdlib\ArrayObject $object
     * @return \Zend\Stdlib\ArrayObject
     */
    public function filter($object)
    {
        $keys = [
            'hearingData',
            'licenceData',
            'publicationSectionConst',
            'case'
        ];

        foreach ($keys as $key) {
            if ($object->offsetExists($key)) {
                $object->offsetUnset($key);
            }
        }

        return $object;
    }
}

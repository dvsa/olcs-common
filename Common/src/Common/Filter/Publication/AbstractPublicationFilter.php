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

    const GV_LIC_TYPE = 'lcat_gv';
    const PSV_LIC_TYPE = 'lcat_psv';

    const APP_NEW_STATUS = 'apsts_consideration';
    const APP_GRANTED_STATUS = 'apsts_granted';
    const APP_REFUSED_STATUS = 'apsts_refused';
    const APP_WITHDRAWN_STATUS = 'apsts_withdrawn';
    const APP_NTU_STATUS = 'apsts_ntu';
    const APP_CURTAILED_STATUS = 'apsts_curtailed';

    const APP_NEW_SECTION = 1;
    const APP_GRANTED_SECTION = 4;
    const APP_REFUSED_SECTION = 5;
    const APP_WITHDRAWN_SECTION = 6;

    const LIC_SURRENDERED_SECTION = 10;
    const LIC_TERMINATED_SECTION = 11;
    const LIC_REVOKED_SECTION = 12;
    const LIC_CNS_SECTION = 20;

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

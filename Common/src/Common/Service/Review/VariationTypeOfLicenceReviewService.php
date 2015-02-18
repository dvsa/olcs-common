<?php

/**
 * Variation Type Of Licence Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Variation Type Of Licence Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationTypeOfLicenceReviewService extends AbstractReviewService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        return ['freetext' => $this->getFreeText($data)];
    }

    private function getFreeText($data)
    {
        return $this->getServiceLocator()->get('Helper\Translation')->translateReplace(
            'variation-application-type-of-licence-freetext',
            [
                $data['licence']['licenceType']['description'],
                $data['licenceType']['description']
            ]
        );
    }
}

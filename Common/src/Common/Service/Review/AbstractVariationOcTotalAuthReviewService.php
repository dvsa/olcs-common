<?php

/**
 * Abstract Variation Oc Total Auth Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Abstract Variation Oc Total Auth Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractVariationOcTotalAuthReviewService extends AbstractReviewService implements
    ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Get the keys of the values to compare
     *
     * @param array $data
     * @return string
     */
    abstract protected function getChangedKeys($data);

    /**
     * Get total auth config
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        $config = null;

        $changes = [];

        foreach ($this->getChangedKeys($data) as $key => $translationIndex) {

            $message = $this->getValueChangedMessage($data, $key);

            if ($message !== null) {
                $changes[] = [
                    'label' => 'review-operating-centres-authorisation-' . $translationIndex,
                    'value' => $message
                ];
            }
        }

        if (!empty($changes)) {
            $config = [
                'header' => 'review-operating-centres-authorisation-title',
                'multiItems' => [
                    $changes
                ]
            ];
        }

        return $config;
    }

    /**
     * If the value has changed on the application, return a translated message
     * otherwise return null
     *
     * @param array $data
     * @param string $key
     * @return null
     */
    private function getValueChangedMessage($data, $key)
    {
        if ($data[$key] == $data['licence'][$key]) {
            return null;
        }

        if ((int)$data[$key] > (int)$data['licence'][$key]) {
            $change = 'increased';
        } else {
            $change = 'decreased';
        }

        $translator = $this->getServiceLocator()->get('Helper\Translation');

        return $translator->translateReplace('review-value-' . $change, [$data['licence'][$key], $data[$key]]);
    }
}

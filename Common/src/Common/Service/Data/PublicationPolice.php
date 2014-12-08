<?php

/**
 * Publication Police service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Service\Data;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\Util\RestClient;
use Common\Data\Object\PublicationPolice as PoliceObject;

/**
 * Publication Police service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PublicationPolice extends AbstractData implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var string
     */
    protected $serviceName = 'PublicationPoliceData';

    /**
     * @return \Common\Data\Object\PublicationPolice
     */
    public function createEmpty()
    {
        return new PoliceObject();
    }

    public function createWithData($data)
    {
        $police = $this->createEmpty();

        foreach ($data as $key => $value) {
            $police->offsetSet($key, $value);
        }

        return $police;
    }

    /**
     * @param \Common\Data\Object\PublicationPolice $police
     * @return mixed
     */
    public function save(PoliceObject $police)
    {
        $method = 'POST';

        if ($police->offsetGet('id')) {
            $method = 'PUT';
        }

        return $this->getServiceLocator()->get('Helper\Rest')->makeRestCall(
            'PublicationPoliceData',
            $method,
            $police->getArrayCopy()
        );
    }

    /**
     * @param \Common\Data\Object\PublicationPolice $police
     * @param string $service
     * @return mixed
     */
    public function create(PoliceObject $police, $service)
    {
        $police = $this->getServiceLocator()->get($service)->filter($police);
        return $this->save($police);
    }

    /**
     * @param array $params
     * @param null $bundle
     * @return mixed
     */
    public function fetchList($params = [], $bundle = null)
    {
        $params['bundle'] = json_encode(empty($bundle) ? $this->getBundle() : $bundle);
        return $this->getRestClient()->get($params);
    }

    /**
     * @return array
     */
    private function getBundle()
    {
        return [
            'properties' => 'ALL',
        ];
    }
}
